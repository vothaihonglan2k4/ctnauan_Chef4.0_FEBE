<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ForumCommentSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id')->toArray();
        $postIds = DB::table('forum_posts')->pluck('id')->toArray();

        if (empty($userIds) || empty($postIds)) {
            $this->command->warn('Không có users hoặc forum_posts trong database. Vui lòng tạo trước.');
            return;
        }

        $comments = [
            [
                'post_id' => $postIds[0] ?? 1,
                'user_id' => $userIds[array_rand($userIds)],
                'parent_id' => null,
                'content' => 'Cảm ơn bạn đã chia sẻ công thức này! Mình sẽ thử làm ngay hôm nay.'
            ],
            [
                'post_id' => $postIds[0] ?? 1,
                'user_id' => $userIds[array_rand($userIds)],
                'parent_id' => null,
                'content' => 'Công thức rất chi tiết và dễ hiểu. Mình đã làm thành công rồi!'
            ],
            [
                'post_id' => $postIds[1] ?? 2,
                'user_id' => $userIds[array_rand($userIds)],
                'parent_id' => null,
                'content' => 'Mình cũng gặp vấn đề này. Bạn có thể cho mình biết thêm chi tiết không?'
            ],
            [
                'post_id' => $postIds[1] ?? 2,
                'user_id' => $userIds[array_rand($userIds)],
                'parent_id' => null,
                'content' => 'Thử dùng lò nướng ở nhiệt độ 180 độ C, nướng khoảng 25-30 phút là được.'
            ],
            [
                'post_id' => $postIds[2] ?? 3,
                'user_id' => $userIds[array_rand($userIds)],
                'parent_id' => null,
                'content' => 'Công thức này giống hệt như cách mình làm. Rất tuyệt vời!'
            ],
            [
                'post_id' => $postIds[2] ?? 3,
                'user_id' => $userIds[array_rand($userIds)],
                'parent_id' => null,
                'content' => 'Bạn có thể nói rõ hơn về cách ướp gà không? Bao lâu là đủ?'
            ],
            [
                'post_id' => $postIds[3] ?? 4,
                'user_id' => $userIds[array_rand($userIds)],
                'parent_id' => null,
                'content' => 'Mẹo rất hữu ích! Mình sẽ áp dụng ngay cho rau của mình.'
            ],
            [
                'post_id' => $postIds[4] ?? 5,
                'user_id' => $userIds[array_rand($userIds)],
                'parent_id' => null,
                'content' => 'Con mình cũng rất kén ăn. Mình sẽ thử những gợi ý này.'
            ],
            [
                'post_id' => $postIds[5] ?? 6,
                'user_id' => $userIds[array_rand($userIds)],
                'parent_id' => null,
                'content' => 'Công thức nước mắm chấm này rất chuẩn! Cảm ơn bạn.'
            ],
            [
                'post_id' => $postIds[6] ?? 7,
                'user_id' => $userIds[array_rand($userIds)],
                'parent_id' => null,
                'content' => 'Mình mới mua nồi chiên không dầu, bài viết này rất hữu ích!'
            ],
        ];

        foreach ($comments as $comment) {
            DB::table('forum_comments')->insert($comment);
        }

        $this->command->info('Đã tạo ' . count($comments) . ' bình luận diễn đàn.');
    }
}
