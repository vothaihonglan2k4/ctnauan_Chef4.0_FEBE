<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rating\StoreRatingRequest;
use App\Models\Rating;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Add or update rating for a recipe
     */
    public function store(StoreRatingRequest $request, $recipeId)
    {
        $recipe = Recipe::findOrFail($recipeId);
        $user = $request->user();

        // Check if user already rated this recipe
        $existingRating = Rating::where('recipe_id', $recipe->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingRating) {
            // Update existing rating
            $existingRating->update([
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);

            return response()->json([
                'message' => 'Đánh giá đã được cập nhật',
                'rating' => $existingRating
            ]);
        } else {
            // Create new rating
            $rating = Rating::create([
                'recipe_id' => $recipe->id,
                'user_id' => $user->id,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);

            return response()->json([
                'message' => 'Đánh giá đã được thêm',
                'rating' => $rating
            ], 201);
        }
    }

    /**
     * Get ratings for a recipe
     */
    public function index($recipeId)
    {
        $recipe = Recipe::findOrFail($recipeId);
        
        $ratings = Rating::with('user')
            ->where('recipe_id', $recipe->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $averageRating = $ratings->avg('rating');
        $ratingDistribution = [
            5 => $ratings->where('rating', 5)->count(),
            4 => $ratings->where('rating', 4)->count(),
            3 => $ratings->where('rating', 3)->count(),
            2 => $ratings->where('rating', 2)->count(),
            1 => $ratings->where('rating', 1)->count()
        ];

        return response()->json([
            'average_rating' => round($averageRating ?? 0, 1),
            'total_ratings' => $ratings->count(),
            'rating_distribution' => $ratingDistribution,
            'ratings' => $ratings->map(function($rating) {
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
            })
        ]);
    }

    /**
     * Delete rating
     */
    public function destroy(Request $request, $recipeId, $ratingId)
    {
        $rating = Rating::where('id', $ratingId)
            ->where('recipe_id', $recipeId)
            ->firstOrFail();

        // Check authorization
        if ($rating->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Bạn không có quyền xóa đánh giá này'
            ], 403);
        }

        $rating->delete();

        return response()->json([
            'message' => 'Đánh giá đã được xóa'
        ]);
    }

    /**
     * Get user's rating for a recipe
     */
    public function getUserRating(Request $request, $recipeId)
    {
        $rating = Rating::where('recipe_id', $recipeId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$rating) {
            return response()->json([
                'rating' => null
            ]);
        }

        return response()->json([
            'rating' => [
                'id' => $rating->id,
                'rating' => $rating->rating,
                'comment' => $rating->comment,
                'created_at' => $rating->created_at
            ]
        ]);
    }
}