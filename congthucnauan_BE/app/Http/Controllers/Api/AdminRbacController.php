<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class AdminRbacController extends Controller
{
    // ============ ROLES ============
    
    public function getRoles()
    {
        $roles = Role::all();
        return response()->json(['roles' => $roles]);
    }

    public function getRole($roleId)
    {
        $role = Role::with('permissions:id,code,module,action,display_name')->findOrFail($roleId);
        return response()->json($role);
    }

    public function createRole(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'unique:roles,name', 'max:50'],
            'display_name' => ['required', 'string', 'max:100'],
        ]);

        $role = Role::create($data);

        return response()->json([
            'message' => 'Tạo role thành công',
            'role' => $role
        ], 201);
    }

    public function updateRole(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);

        // Chặn sửa admin role
        if ($role->name === 'admin') {
            return response()->json(['message' => 'Không cho phép chỉnh sửa role Admin'], 422);
        }

        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:100'],
        ]);

        $role->update($data);

        return response()->json([
            'message' => 'Cập nhật role thành công',
            'role' => $role
        ]);
    }

    public function deleteRole($roleId)
    {
        $role = Role::findOrFail($roleId);

        // Chặn xóa admin role
        if ($role->name === 'admin') {
            return response()->json(['message' => 'Không cho phép xóa role Admin'], 422);
        }

        // Kiểm tra xem có user nào đang dùng role này không
        if ($role->users()->count() > 0) {
            return response()->json(['message' => 'Không thể xóa role đang được sử dụng'], 422);
        }

        $role->delete();

        return response()->json(['message' => 'Xóa role thành công']);
    }

    // ============ PERMISSIONS ============
    
    public function getPermissions()
    {
        $permissions = Permission::all();
        return response()->json(['permissions' => $permissions]);
    }

    public function getPermissionsByModule()
    {
        $permissions = Permission::all()->groupBy('module');
        return response()->json($permissions);
    }

    public function createPermission(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'unique:permissions,code', 'max:100'],
            'module' => ['required', 'string', 'max:50'],
            'action' => ['required', 'string', 'max:50'],
            'display_name' => ['required', 'string', 'max:100'],
        ]);

        $permission = Permission::create($data);

        return response()->json([
            'message' => 'Tạo permission thành công',
            'permission' => $permission
        ], 201);
    }

    public function updatePermission(Request $request, $permissionId)
    {
        $permission = Permission::findOrFail($permissionId);

        $data = $request->validate([
            'module' => ['required', 'string', 'max:50'],
            'action' => ['required', 'string', 'max:50'],
            'display_name' => ['required', 'string', 'max:100'],
        ]);

        $permission->update($data);

        return response()->json([
            'message' => 'Cập nhật permission thành công',
            'permission' => $permission
        ]);
    }

    public function deletePermission($permissionId)
    {
        $permission = Permission::findOrFail($permissionId);

        // Kiểm tra xem có role nào đang dùng permission này không
        if ($permission->roles()->count() > 0) {
            return response()->json(['message' => 'Không thể xóa permission đang được sử dụng'], 422);
        }

        $permission->delete();

        return response()->json(['message' => 'Xóa permission thành công']);
    }

    // ============ ROLE PERMISSIONS ============
    
    public function getRolePermissions($roleId)
    {
        $role = Role::with('permissions:id,code,module,action,display_name')->findOrFail($roleId);
        return response()->json(['role' => $role]);
    }

    public function updateRolePermissions(Request $request, $roleId)
    {
        $data = $request->validate([
            'permission_ids' => ['required', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::findOrFail($roleId);

        // Chặn sửa admin role
        if ($role->name === 'admin') {
            return response()->json(['message' => 'Không cho phép chỉnh quyền Admin'], 422);
        }

        $role->permissions()->sync($data['permission_ids']);

        return response()->json(['message' => 'Cập nhật quyền role thành công']);
    }

    // ============ USER ROLES ============
    
    public function getUserRoles($userId)
    {
        $user = User::with(['roles' => function($query) {
            $query->select('roles.id', 'roles.name', 'roles.display_name')
                  ->withPivot('branch_id');
        }])->findOrFail($userId);

        return response()->json($user);
    }

    public function assignUserRole(Request $request, $userId)
    {
        $data = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'branch_id' => ['nullable', 'integer'],
        ]);

        $user = User::findOrFail($userId);

        // Kiểm tra xem user đã có role này chưa
        $existingRole = $user->roles()
            ->where('role_id', $data['role_id'])
            ->wherePivot('branch_id', $data['branch_id'] ?? null)
            ->exists();

        if ($existingRole) {
            return response()->json(['message' => 'User đã có role này rồi'], 422);
        }

        $user->roles()->attach($data['role_id'], ['branch_id' => $data['branch_id'] ?? null]);

        return response()->json(['message' => 'Gán role cho user thành công']);
    }

    public function removeUserRole(Request $request, $userId)
    {
        $data = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'branch_id' => ['nullable', 'integer'],
        ]);

        $user = User::findOrFail($userId);

        // Không cho phép xóa role admin của user
        $role = Role::find($data['role_id']);
        if ($role && $role->name === 'admin') {
            return response()->json(['message' => 'Không cho phép xóa role Admin'], 422);
        }

        $user->roles()
            ->wherePivot('role_id', $data['role_id'])
            ->wherePivot('branch_id', $data['branch_id'] ?? null)
            ->detach();

        return response()->json(['message' => 'Xóa role của user thành công']);
    }

    // ============ UTILITY ============
    
    public function getAllRolesWithPermissions()
    {
        $roles = Role::with('permissions')->get();
        return response()->json($roles);
    }

    public function getUsersWithRoles()
    {
        $users = User::with(['roles' => function($query) {
            $query->select('roles.id', 'roles.name', 'roles.display_name')
                  ->withPivot('branch_id');
        }])->get();

        return response()->json($users);
    }
}
