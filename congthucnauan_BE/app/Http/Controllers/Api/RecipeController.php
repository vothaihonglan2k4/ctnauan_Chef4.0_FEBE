<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recipe\StoreRecipeRequest;
use App\Http\Requests\Recipe\UpdateRecipeRequest;
use App\Models\Recipe;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    /**
     * Get all recipes (with filters)
     */
    public function index(Request $request)
    {
        $query = Recipe::with(['user', 'category'])
            ->approved();

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by title or ingredients
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ingredients', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Sort options
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if ($sortBy === 'rating') {
            // Sort by average rating
            $query->withAvg('ratings', 'rating')
                  ->orderBy('ratings_avg_rating', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 12);
        $recipes = $query->paginate($perPage);

        return response()->json([
            'recipes' => $recipes->items(),
            'pagination' => [
                'current_page' => $recipes->currentPage(),
                'last_page' => $recipes->lastPage(),
                'per_page' => $recipes->perPage(),
                'total' => $recipes->total()
            ]
        ]);
    }

    /**
     * Get single recipe
     */
    public function show($id)
    {
        $recipe = Recipe::with(['user', 'category', 'ratings.user'])
            ->withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->findOrFail($id);

        return response()->json([
            'recipe' => [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'description' => $recipe->description,
                'ingredients' => $recipe->ingredients,
                'instructions' => $recipe->instructions,
                'cooking_time' => $recipe->cooking_time,
                'servings' => $recipe->servings,
                'difficulty' => $recipe->difficulty,
                'image' => $recipe->image,
                'video_url' => $recipe->video_url,
                'status' => $recipe->status,
                'category_id' => $recipe->category_id,
                'user_id' => $recipe->user_id,
                'average_rating' => round($recipe->ratings_avg_rating ?? 0, 1),
                'total_ratings' => $recipe->ratings_count,
                'user' => [
                    'id' => $recipe->user->id,
                    'name' => $recipe->user->name,
                    'email' => $recipe->user->email,
                    'avatar' => $recipe->user->avatar
                ],
                'category' => $recipe->category ? [
                    'id' => $recipe->category->id,
                    'name' => $recipe->category->name
                ] : null,
                'ratings' => $recipe->ratings->map(function($rating) {
                    return [
                        'id' => $rating->id,
                        'rating' => $rating->rating,
                        'comment' => $rating->comment,
                        'user_id' => $rating->user_id,
                        'user' => [
                            'id' => $rating->user->id,
                            'name' => $rating->user->name,
                            'avatar' => $rating->user->avatar
                        ],
                        'created_at' => $rating->created_at
                    ];
                }),
                'created_at' => $recipe->created_at,
                'updated_at' => $recipe->updated_at
            ]
        ]);
    }

    /**
     * Create new recipe
     */
    public function store(StoreRecipeRequest $request)
    {
        $data = $request->only([
            'title', 'description', 'ingredients', 'instructions',
            'category_id', 'cooking_time', 'servings', 'difficulty', 'video_url'
        ]);

        $data['user_id'] = $request->user()->id;
        $data['status'] = 'pending'; // Require admin approval

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads'), $filename);
            $data['image'] = $filename;
        }

        $recipe = Recipe::create($data);

        return response()->json([
            'message' => 'Công thức đã được tạo và đang chờ duyệt',
            'recipe' => $recipe
        ], 201);
    }

    /**
     * Update recipe
     */
    public function update(UpdateRecipeRequest $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

        $data = $request->only([
            'title', 'description', 'ingredients', 'instructions',
            'category_id', 'cooking_time', 'servings', 'difficulty', 'video_url'
        ]);

        // Set back to pending if content changed
        if (!$request->user()->isAdmin()) {
            $data['status'] = 'pending';
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($recipe->image && file_exists(public_path('uploads/' . $recipe->image))) {
                unlink(public_path('uploads/' . $recipe->image));
            }

            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads'), $filename);
            $data['image'] = $filename;
        }

        $recipe->update($data);

        return response()->json([
            'message' => 'Công thức đã được cập nhật',
            'recipe' => $recipe
        ]);
    }

    /**
     * Delete recipe
     */
    public function destroy(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

        // Check authorization
        if ($recipe->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Bạn không có quyền xóa công thức này'
            ], 403);
        }

        // Delete image
        if ($recipe->image && file_exists(public_path('uploads/' . $recipe->image))) {
            unlink(public_path('uploads/' . $recipe->image));
        }

        $recipe->delete();

        return response()->json([
            'message' => 'Công thức đã được xóa'
        ]);
    }

    /**
     * Get user's recipes
     */
    public function myRecipes(Request $request)
    {
        try {
            $recipes = Recipe::with(['category'])
                ->where('user_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            return response()->json([
                'recipes' => $recipes->items(),
                'pagination' => [
                    'current_page' => $recipes->currentPage(),
                    'last_page' => $recipes->lastPage(),
                    'per_page' => $recipes->perPage(),
                    'total' => $recipes->total()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching my recipes',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get all categories
     */
    public function categories()
    {
        $categories = Category::withCount('recipes')->get();

        return response()->json([
            'categories' => $categories
        ]);
    }
}