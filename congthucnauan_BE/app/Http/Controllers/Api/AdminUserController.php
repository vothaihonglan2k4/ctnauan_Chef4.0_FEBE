<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * Get all users
     */
    public function index(Request $request)
    {
        try {
            $users = User::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single user
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            
            return response()->json([
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không tìm thấy người dùng',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Create new user
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role
            ]);

            return response()->json([
                'message' => 'Thêm người dùng thành công',
                'user' => $user
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi thêm người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Check if admin is trying to demote themselves
            if ($request->user()->id == $id && $request->role !== 'admin') {
                return response()->json([
                    'message' => 'Bạn không thể tự chuyển quyền admin của mình'
                ], 403);
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role
            ]);

            return response()->json([
                'message' => 'Cập nhật người dùng thành công',
                'user' => $user
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function destroy(Request $request, $id)
    {
        try {
            // Check if admin is trying to delete themselves
            if ($request->user()->id == $id) {
                return response()->json([
                    'message' => 'Bạn không thể xóa tài khoản của chính mình'
                ], 403);
            }

            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'message' => 'Xóa người dùng thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi xóa người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
