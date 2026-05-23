<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportSqlDumpSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            $this->truncateTables();
            $this->seedCategories();
            $this->seedUsers();
            $this->seedRecipes();
            $this->seedRatings();
            $this->seedClassrooms();
            $this->seedCourses();
            $this->seedCourseLessons();
            $this->seedPayments();
            $this->seedCourseEnrollments();
            $this->seedCourseLessonCompletions();
            $this->seedForumTags();
            $this->seedForumPosts();
            $this->seedForumPostTags();
            $this->seedForumComments();
            $this->seedForumLikes();
            $this->seedContacts();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function truncateTables(): void
    {
        $tables = [
            'forum_post_tags',
            'forum_likes',
            'forum_comments',
            'forum_posts',
            'forum_tags',
            'course_lesson_completion',
            'course_lessons',
            'course_enrollments',
            'courses',
            'classrooms',
            'ratings',
            'recipes',
            'contacts',
            'payments',
            'users',
            'categories',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }

    private function seedCategories(): void
    {
        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Món Việt Nam', 'created_at' => now()],
            ['id' => 2, 'name' => 'Món Á', 'created_at' => now()],
            ['id' => 3, 'name' => 'Món Âu', 'created_at' => now()],
            ['id' => 4, 'name' => 'Món chay', 'created_at' => now()],
            ['id' => 5, 'name' => 'Đồ uống', 'created_at' => now()],
        ]);
    }

    private function seedUsers(): void
    {
        $password = Hash::make('lan12345');

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Admin Demo',
                'email' => 'admin@vothaihonglan.net',
                'phone' => '0900000001',
                'address' => 'Đà Nẵng',
                'avatar' => 'default-avatar.png',
                'password' => $password,
                'role' => 'admin',
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Manager Demo',
                'email' => 'manager@vothaihonglan.net',
                'phone' => '0900000002',
                'address' => 'Đà Nẵng',
                'avatar' => 'default-avatar.png',
                'password' => $password,
                'role' => 'manager',
                'created_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Nguyễn Văn A',
                'email' => 'user.a@vothaihonglan.net',
                'phone' => '0900000003',
                'address' => 'Hà Nội',
                'avatar' => 'default-avatar.png',
                'password' => $password,
                'role' => 'user',
                'created_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Trần Thị B',
                'email' => 'user.b@vothaihonglan.net',
                'phone' => '0900000004',
                'address' => 'TP.HCM',
                'avatar' => 'default-avatar.png',
                'password' => $password,
                'role' => 'user',
                'created_at' => now(),
            ],
        ]);
    }

    private function seedRecipes(): void
    {
        DB::table('recipes')->insert([
            [
                'id' => 1,
                'title' => 'Phở bò truyền thống',
                'description' => 'Món phở bò với nước dùng trong và thơm.',
                'ingredients' => "- 500g xương bò\n- 300g thịt bò\n- Gừng, hành, quế, hồi",
                'instructions' => "1. Ninh xương 3 giờ\n2. Nêm gia vị\n3. Trụng bánh phở và chan nước dùng",
                'image' => 'no-image.jpg',
                'video_url' => null,
                'category_id' => 1,
                'user_id' => 3,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'title' => 'Sushi cuộn cơ bản',
                'description' => 'Công thức sushi phù hợp cho người mới.',
                'ingredients' => "- Cơm sushi\n- Rong biển\n- Cá hồi\n- Dưa leo",
                'instructions' => "1. Nấu cơm\n2. Trải rong biển và cơm\n3. Cuộn và cắt khoanh",
                'image' => 'no-image.jpg',
                'video_url' => null,
                'category_id' => 2,
                'user_id' => 4,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'title' => 'Pasta sốt kem nấm',
                'description' => 'Pasta béo nhẹ, dễ làm tại nhà.',
                'ingredients' => "- Mì pasta\n- Nấm\n- Kem tươi\n- Phô mai",
                'instructions' => "1. Luộc mì\n2. Xào nấm\n3. Nấu sốt kem và trộn mì",
                'image' => 'no-image.jpg',
                'video_url' => null,
                'category_id' => 3,
                'user_id' => 3,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedRatings(): void
    {
        DB::table('ratings')->insert([
            ['id' => 1, 'recipe_id' => 1, 'user_id' => 4, 'rating' => 5, 'comment' => 'Rất ngon, dễ làm.', 'created_at' => now()],
            ['id' => 2, 'recipe_id' => 2, 'user_id' => 3, 'rating' => 4, 'comment' => 'Ổn, cần luyện tay cuộn.', 'created_at' => now()],
            ['id' => 3, 'recipe_id' => 3, 'user_id' => 4, 'rating' => 5, 'comment' => 'Mùi vị rất cân bằng.', 'created_at' => now()],
        ]);
    }

    private function seedClassrooms(): void
    {
        DB::table('classrooms')->insert([
            [
                'id' => 1,
                'name' => 'Phòng online cơ bản',
                'description' => 'Dành cho khóa học nhập môn',
                'capacity' => 50,
                'location' => 'Online',
                'active' => 1,
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Phòng online nâng cao',
                'description' => 'Dành cho khóa học nâng cao',
                'capacity' => 30,
                'location' => 'Online',
                'active' => 1,
                'created_at' => now(),
            ],
        ]);
    }

    private function seedCourses(): void
    {
        DB::table('courses')->insert([
            [
                'id' => 1,
                'title' => 'Nấu ăn cơ bản cho người mới bắt đầu',
                'description' => 'Khóa học dành cho những người mới bắt đầu học nấu ăn, giới thiệu các kỹ thuật cơ bản và công thức đơn giản',
                'price' => 299000,
                'duration' => 720,
                'level' => 'beginner',
                'classroom_id' => 1,
                'user_id' => 1,
                'image' => '1745825702_giai-ngan-ngay-tet-voi-mon-salad-hoa-qua-kieu-han-quoc-202205241325570525.jpg',
                'status' => 'published',
                'requirements' => '',
                'what_will_learn' => '',
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'title' => 'Món ăn Á - Âu fusion',
                'description' => 'Khóa học kết hợp kỹ thuật nấu ăn Á và Âu để tạo ra những món ăn sáng tạo và độc đáo',
                'price' => 499000,
                'duration' => 960,
                'level' => 'intermediate',
                'classroom_id' => 2,
                'user_id' => 1,
                'image' => '1744090962_images.jfif',
                'status' => 'published',
                'requirements' => '',
                'what_will_learn' => '',
                'created_at' => now(),
            ],
            [
                'id' => 3,
                'title' => 'Làm bánh chuyên nghiệp',
                'description' => 'Khóa học làm bánh từ cơ bản đến nâng cao, hướng dẫn chi tiết các loại bánh phổ biến',
                'price' => 699000,
                'duration' => 1200,
                'level' => 'advanced',
                'classroom_id' => 2,
                'user_id' => 1,
                'image' => '1744091165_image.jpeg',
                'status' => 'published',
                'requirements' => 'Đã có kinh nghiệm làm bánh cơ bản',
                'what_will_learn' => 'Kỹ thuật làm các loại bánh Âu, Trang trí bánh nghệ thuật, Làm socola thủ công',
                'created_at' => now(),
            ],
        ]);
    }

    private function seedCourseLessons(): void
    {
        DB::table('course_lessons')->insert([
            [
                'id' => 1,
                'course_id' => 1,
                'title' => 'Giới thiệu dụng cụ bếp',
                'content' => 'Làm quen dao, thớt và dụng cụ cơ bản.',
                'video_url' => null,
                'duration_minutes' => 20,
                'sort_order' => 1,
                'is_free' => 1,
                'image' => 'no-image.jpg',
                'summary' => 'Nắm được bộ dụng cụ cần có cho người mới.',
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'course_id' => 1,
                'title' => 'Kỹ thuật cắt thái cơ bản',
                'content' => 'Thực hành cắt hạt lựu, thái sợi, thái lát.',
                'video_url' => null,
                'duration_minutes' => 35,
                'sort_order' => 2,
                'is_free' => 0,
                'image' => 'no-image.jpg',
                'summary' => 'Giảm thời gian sơ chế, tăng tính thẩm mỹ món ăn.',
                'created_at' => now(),
            ],
            [
                'id' => 3,
                'course_id' => 2,
                'title' => 'Nước sốt nền món Á',
                'content' => 'Pha các loại sốt nền thường dùng.',
                'video_url' => null,
                'duration_minutes' => 40,
                'sort_order' => 1,
                'is_free' => 1,
                'image' => 'no-image.jpg',
                'summary' => 'Biết pha sốt nền để ứng dụng đa món.',
                'created_at' => now(),
            ],
        ]);
    }

    private function seedPayments(): void
    {
        DB::table('payments')->insert([
            [
                'id' => 1,
                'user_id' => 3,
                'amount' => 299000,
                'payment_method' => 'demo',
                'status' => 'completed',
                'transaction_id' => 'DEMO_PAY_0001',
                'gateway_transaction_id' => null,
                'gateway_response' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'user_id' => 4,
                'amount' => 499000,
                'payment_method' => 'demo',
                'status' => 'completed',
                'transaction_id' => 'DEMO_PAY_0002',
                'gateway_transaction_id' => null,
                'gateway_response' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedCourseEnrollments(): void
    {
        DB::table('course_enrollments')->insert([
            [
                'id' => 1,
                'user_id' => 3,
                'course_id' => 1,
                'payment_id' => 1,
                'enrollment_date' => now(),
                'completion_date' => null,
                'progress' => 40,
                'status' => 'active',
            ],
            [
                'id' => 2,
                'user_id' => 4,
                'course_id' => 2,
                'payment_id' => 2,
                'enrollment_date' => now(),
                'completion_date' => null,
                'progress' => 20,
                'status' => 'active',
            ],
        ]);
    }

    private function seedCourseLessonCompletions(): void
    {
        DB::table('course_lesson_completion')->insert([
            [
                'id' => 1,
                'user_id' => 3,
                'course_id' => 1,
                'lesson_id' => 1,
                'completed_at' => now(),
            ],
            [
                'id' => 2,
                'user_id' => 4,
                'course_id' => 2,
                'lesson_id' => 3,
                'completed_at' => now(),
            ],
        ]);
    }

    private function seedForumTags(): void
    {
        DB::table('forum_tags')->insert([
            ['id' => 1, 'name' => 'Món Việt', 'slug' => 'mon-viet', 'created_at' => now()],
            ['id' => 2, 'name' => 'Hỏi đáp', 'slug' => 'hoi-dap', 'created_at' => now()],
            ['id' => 3, 'name' => 'Bí quyết', 'slug' => 'bi-quyet', 'created_at' => now()],
        ]);
    }

    private function seedForumPosts(): void
    {
        DB::table('forum_posts')->insert([
            [
                'id' => 1,
                'user_id' => 3,
                'title' => 'Mẹo ninh nước dùng phở trong và ngọt',
                'content' => 'Chần xương kỹ, ninh lửa nhỏ và hớt bọt đều để nước dùng trong.',
                'image' => null,
                'video_url' => null,
                'recipe_id' => 1,
                'views' => 25,
                'is_pinned' => 1,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'user_id' => 4,
                'title' => 'Lần đầu cuộn sushi hay bị bung, xử lý sao?',
                'content' => 'Mọi người có mẹo giữ cuộn chắc tay mà không nát cơm không?',
                'image' => null,
                'video_url' => null,
                'recipe_id' => 2,
                'views' => 12,
                'is_pinned' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedForumPostTags(): void
    {
        DB::table('forum_post_tags')->insert([
            ['post_id' => 1, 'tag_id' => 1],
            ['post_id' => 1, 'tag_id' => 3],
            ['post_id' => 2, 'tag_id' => 2],
        ]);
    }

    private function seedForumComments(): void
    {
        DB::table('forum_comments')->insert([
            [
                'id' => 1,
                'post_id' => 1,
                'user_id' => 4,
                'parent_id' => null,
                'content' => 'Mình làm theo và nước dùng thơm hơn hẳn.',
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'post_id' => 1,
                'user_id' => 3,
                'parent_id' => 1,
                'content' => 'Cảm ơn bạn, nhớ thêm gừng nướng nhé.',
                'created_at' => now(),
            ],
            [
                'id' => 3,
                'post_id' => 2,
                'user_id' => 3,
                'parent_id' => null,
                'content' => 'Bạn thử giảm lượng cơm và cuộn dứt khoát hơn.',
                'created_at' => now(),
            ],
        ]);
    }

    private function seedForumLikes(): void
    {
        DB::table('forum_likes')->insert([
            ['id' => 1, 'post_id' => 1, 'user_id' => 3, 'created_at' => now()],
            ['id' => 2, 'post_id' => 1, 'user_id' => 4, 'created_at' => now()],
            ['id' => 3, 'post_id' => 2, 'user_id' => 3, 'created_at' => now()],
        ]);
    }

    private function seedContacts(): void
    {
        DB::table('contacts')->insert([
            [
                'id' => 1,
                'name' => 'Khách hàng demo',
                'email' => 'khachhang.demo@example.com',
                'subject' => 'Hỗ trợ đăng ký khóa học',
                'message' => 'Mình muốn biết cách đăng ký khóa học nâng cao.',
                'status' => 'new',
                'created_at' => now(),
            ],
        ]);
    }
}
