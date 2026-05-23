<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    /**
     * Get all categories
     */
    public function index()
    {
        try {
            $categories = Category::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách danh mục',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new category
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = Category::create([
                'name' => $request->name
            ]);

            return response()->json([
                'message' => 'Thêm danh mục thành công',
                'category' => $category
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi thêm danh mục',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update category
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $category->update([
                'name' => $request->name
            ]);

            return response()->json([
                'message' => 'Cập nhật danh mục thành công',
                'category' => $category
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật danh mục',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete category
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            // Check if category has recipes
            $recipeCount = $category->recipes()->count();
            if ($recipeCount > 0) {
                return response()->json([
                    'message' => "Không thể xóa danh mục này vì có {$recipeCount} công thức đang sử dụng"
                ], 400);
            }

            $category->delete();

            return response()->json([
                'message' => 'Xóa danh mục thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi xóa danh mục',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
