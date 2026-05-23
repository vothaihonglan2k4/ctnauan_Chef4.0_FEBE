<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeCommentSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id')->toArray();
        $recipeIds = DB::table('recipes')->pluck('id')->toArray();

        if (empty($userIds) || empty($recipeIds)) {
            $this->command->warn('Không có users hoặc recipes trong database. Vui lòng tạo trước.');
            return;
        }

        $ratings = [];
        $usedCombinations = [];

        // Tạo ratings với các kết hợp recipe_id và user_id khác nhau
        foreach ($recipeIds as $recipeId) {
            foreach ($userIds as $userId) {
                $key = $recipeId . '-' . $userId;
                if (!in_array($key, $usedCombinations)) {
                    $ratings[] = [
                        'recipe_id' => $recipeId,
                        'user_id' => $userId,
                        'rating' => rand(3, 5),
                        'comment' => $this->getRandomComment()
                    ];
                    $usedCombinations[] = $key;

                    if (count($ratings) >= 10) break 2;
                }
            }
        }

        foreach ($ratings as $rating) {
            DB::table('ratings')->insert($rating);
        }

        $this->command->info('Đã tạo ' . count($ratings) . ' bình luận công thức.');
    }

    private function getRandomComment(): string
    {
        $comments = [
            'Công thức rất tuyệt vời! Mình đã làm theo và kết quả tuyệt đẹp. Cảm ơn bạn!',
            'Công thức dễ hiểu, nhưng mình thấy cần thêm một chút muối. Nhìn chung rất tốt.',
            'Đây là công thức tốt nhất mình từng thử! Gia đình mình rất thích.',
            'Công thức ổn, nhưng mình thấy hơi phức tạp. Cần cải thiện hướng dẫn.',
            'Tuyệt vời! Mình đã làm lại nhiều lần rồi. Tất cả mọi người đều yêu thích.',
            'Rất ngon, nhưng thời gian nấu hơi lâu. Có thể rút ngắn được không?',
            'Công thức hoàn hảo! Mình rất hài lòng với kết quả.',
            'Tốt, nhưng mình thêm một số gia vị khác để phù hợp với khẩu vị gia đình.',
            'Xuất sắc! Đây là công thức mình tìm kiếm từ lâu.',
            'Bình thường, không có gì đặc biệt. Nhưng vẫn ổn để thử.',
        ];

        return $comments[array_rand($comments)];
    }
}
