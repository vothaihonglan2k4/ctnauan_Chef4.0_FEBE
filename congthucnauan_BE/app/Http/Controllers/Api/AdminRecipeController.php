<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class AdminRecipeController extends Controller
{
    public function index(Request $request)
    {
        $query = Recipe::with(['user', 'category'])
            ->select('recipes.*');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $query->orderBy('created_at', 'desc');

        $perPage = $request->get('per_page', 10);
        $recipes = $query->paginate($perPage);

        return response()->json([
            'recipes' => $recipes->map(function($recipe) {
                return [
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'author_name' => $recipe->user->name,
                    'category_name' => $recipe->category->name ?? 'N/A',
                    'status' => $recipe->status,
                    'created_at' => $recipe->created_at
                ];
            }),
            'pagination' => [
                'current_page' => $recipes->currentPage(),
                'last_page' => $recipes->lastPage(),
                'per_page' => $recipes->perPage(),
                'total' => $recipes->total()
            ]
        ]);
    }

    public function approve($id)
    {
        $recipe = Recipe::findOrFail($id);

        if ($recipe->status === 'approved') {
            return response()->json([
                'message' => 'Công thức đã được duyệt rồi'
            ], 400);
        }

        $recipe->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Duyệt công thức thành công',
            'recipe' => $recipe
        ]);
    }

    public function reject($id)
    {
        $recipe = Recipe::findOrFail($id);

        if ($recipe->status === 'rejected') {
            return response()->json([
                'message' => 'Công thức đã được từ chối rồi'
            ], 400);
        }

        $recipe->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Từ chối công thức thành công',
            'recipe' => $recipe
        ]);
    }

    public function destroy($id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->delete();

        return response()->json([
            'message' => 'Xóa công thức thành công'
        ]);
    }
}
