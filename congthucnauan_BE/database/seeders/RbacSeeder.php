<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo roles
        $roles = [
            ['id' => 1, 'name' => 'admin', 'display_name' => 'Quản trị viên'],
            ['id' => 2, 'name' => 'manager', 'display_name' => 'Quản lý'],
            ['id' => 3, 'name' => 'user', 'display_name' => 'Người dùng'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insertOrIgnore($role);
        }

        // 2. Tạo permissions
        $permissions = [
            // Quản lý người dùng
            ['code' => 'user.view', 'module' => 'Người dùng', 'action' => 'Xem', 'display_name' => 'Xem danh sách người dùng'],
            ['code' => 'user.create', 'module' => 'Người dùng', 'action' => 'Tạo', 'display_name' => 'Tạo người dùng mới'],
            ['code' => 'user.update', 'module' => 'Người dùng', 'action' => 'Sửa', 'display_name' => 'Cập nhật người dùng'],
            ['code' => 'user.delete', 'module' => 'Người dùng', 'action' => 'Xóa', 'display_name' => 'Xóa người dùng'],

            // Quản lý công thức
            ['code' => 'recipe.view', 'module' => 'Công thức', 'action' => 'Xem', 'display_name' => 'Xem công thức'],
            ['code' => 'recipe.create', 'module' => 'Công thức', 'action' => 'Tạo', 'display_name' => 'Tạo công thức'],
            ['code' => 'recipe.update', 'module' => 'Công thức', 'action' => 'Sửa', 'display_name' => 'Cập nhật công thức'],
            ['code' => 'recipe.delete', 'module' => 'Công thức', 'action' => 'Xóa', 'display_name' => 'Xóa công thức'],
            ['code' => 'recipe.approve', 'module' => 'Công thức', 'action' => 'Duyệt', 'display_name' => 'Duyệt công thức'],

            // Quản lý danh mục
            ['code' => 'category.view', 'module' => 'Danh mục', 'action' => 'Xem', 'display_name' => 'Xem danh mục'],
            ['code' => 'category.create', 'module' => 'Danh mục', 'action' => 'Tạo', 'display_name' => 'Tạo danh mục'],
            ['code' => 'category.update', 'module' => 'Danh mục', 'action' => 'Sửa', 'display_name' => 'Cập nhật danh mục'],
            ['code' => 'category.delete', 'module' => 'Danh mục', 'action' => 'Xóa', 'display_name' => 'Xóa danh mục'],

            // Quản lý khóa học
            ['code' => 'course.view', 'module' => 'Khóa học', 'action' => 'Xem', 'display_name' => 'Xem khóa học'],
            ['code' => 'course.create', 'module' => 'Khóa học', 'action' => 'Tạo', 'display_name' => 'Tạo khóa học'],
            ['code' => 'course.update', 'module' => 'Khóa học', 'action' => 'Sửa', 'display_name' => 'Cập nhật khóa học'],
            ['code' => 'course.delete', 'module' => 'Khóa học', 'action' => 'Xóa', 'display_name' => 'Xóa khóa học'],

            // Quản lý phòng học
            ['code' => 'classroom.view', 'module' => 'Phòng học', 'action' => 'Xem', 'display_name' => 'Xem phòng học'],
            ['code' => 'classroom.create', 'module' => 'Phòng học', 'action' => 'Tạo', 'display_name' => 'Tạo phòng học'],
            ['code' => 'classroom.update', 'module' => 'Phòng học', 'action' => 'Sửa', 'display_name' => 'Cập nhật phòng học'],
            ['code' => 'classroom.delete', 'module' => 'Phòng học', 'action' => 'Xóa', 'display_name' => 'Xóa phòng học'],

            // Quản lý thanh toán
            ['code' => 'payment.view', 'module' => 'Thanh toán', 'action' => 'Xem', 'display_name' => 'Xem thanh toán'],
            ['code' => 'payment.update', 'module' => 'Thanh toán', 'action' => 'Sửa', 'display_name' => 'Cập nhật trạng thái thanh toán'],
            ['code' => 'payment.statistics', 'module' => 'Thanh toán', 'action' => 'Thống kê', 'display_name' => 'Xem thống kê thanh toán'],

            // Quản lý liên hệ
            ['code' => 'contact.view', 'module' => 'Liên hệ', 'action' => 'Xem', 'display_name' => 'Xem liên hệ'],
            ['code' => 'contact.update', 'module' => 'Liên hệ', 'action' => 'Sửa', 'display_name' => 'Cập nhật trạng thái liên hệ'],
            ['code' => 'contact.delete', 'module' => 'Liên hệ', 'action' => 'Xóa', 'display_name' => 'Xóa liên hệ'],

            // Báo cáo
            ['code' => 'report.view', 'module' => 'Báo cáo', 'action' => 'Xem', 'display_name' => 'Xem báo cáo'],
            ['code' => 'report.export', 'module' => 'Báo cáo', 'action' => 'Xuất', 'display_name' => 'Xuất báo cáo'],

            // Diễn đàn
            ['code' => 'forum.view', 'module' => 'Diễn đàn', 'action' => 'Xem', 'display_name' => 'Xem bài viết diễn đàn'],
            ['code' => 'forum.create', 'module' => 'Diễn đàn', 'action' => 'Tạo', 'display_name' => 'Tạo bài viết diễn đàn'],
            ['code' => 'forum.update', 'module' => 'Diễn đàn', 'action' => 'Sửa', 'display_name' => 'Sửa bài viết diễn đàn'],
            ['code' => 'forum.delete', 'module' => 'Diễn đàn', 'action' => 'Xóa', 'display_name' => 'Xóa bài viết diễn đàn'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore($permission);
        }

        // 3. Lấy tất cả permission IDs
        $allPermissionIds = DB::table('permissions')->pluck('id')->toArray();

        // 4. Gán FULL quyền cho Admin (role_id = 1)
        $adminPermissions = [];
        foreach ($allPermissionIds as $permId) {
            $adminPermissions[] = [
                'role_id' => 1,
                'permission_id' => $permId,
            ];
        }
        DB::table('role_permissions')->insertOrIgnore($adminPermissions);

        // 5. Gán quyền cho Manager (role_id = 2) - một số quyền cơ bản
        $managerPermissionCodes = [
            'recipe.view', 'recipe.approve', 'recipe.update', 'recipe.delete',
            'course.view', 'course.create', 'course.update', 'course.delete',
            'category.view', 'category.create', 'category.update', 'category.delete',
            'contact.view', 'contact.update', 'contact.delete',
            'report.view',
            'forum.view', 'forum.delete',
        ];

        $managerPermissionIds = DB::table('permissions')
            ->whereIn('code', $managerPermissionCodes)
            ->pluck('id')
            ->toArray();

        $managerPermissions = [];
        foreach ($managerPermissionIds as $permId) {
            $managerPermissions[] = [
                'role_id' => 2,
                'permission_id' => $permId,
            ];
        }
        DB::table('role_permissions')->insertOrIgnore($managerPermissions);

        // 6. Gán quyền cho User (role_id = 3) - quyền tối thiểu
        $userPermissionCodes = [
            'recipe.view', 'recipe.create',
            'course.view',
            'forum.view', 'forum.create', 'forum.update',
        ];

        $userPermissionIds = DB::table('permissions')
            ->whereIn('code', $userPermissionCodes)
            ->pluck('id')
            ->toArray();

        $userPermissions = [];
        foreach ($userPermissionIds as $permId) {
            $userPermissions[] = [
                'role_id' => 3,
                'permission_id' => $permId,
            ];
        }
        DB::table('role_permissions')->insertOrIgnore($userPermissions);

        $this->command->info('✅ RBAC seeder completed: roles, permissions, and role_permissions created!');
    }
}
