<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class ManagerRecipeController extends Controller
{
    public function index(Request $request)
    {
        $query = Recipe::with(['user', 'category']);
        
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $recipes = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return response()->json([
            'recipes' => $recipes->items(),
            'pagination' => [
                'current_page' => $recipes->currentPage(),
                'last_page' => $recipes->lastPage(),
                'total' => $recipes->total()
            ],
            'stats' => [
                'total' => Recipe::count(),
                'pending' => Recipe::where('status', 'pending')->count(),
                'approved' => Recipe::where('status', 'approved')->count(),
                'rejected' => Recipe::where('status', 'rejected')->count(),
            ]
        ]);
    }

    public function approve($id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->update(['status' => 'approved']);
        
        return response()->json([
            'message' => 'Công thức đã được phê duyệt',
            'recipe' => $recipe
        ]);
    }

    public function reject(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->update(['status' => 'rejected']);
        
        return response()->json([
            'message' => 'Công thức đã bị từ chối',
            'recipe' => $recipe
        ]);
    }
}
