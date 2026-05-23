<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ForumPostSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->warn('Không có users trong database. Vui lòng tạo users trước.');
            return;
        }

        $posts = [
            [
                'title' => 'Bí quyết làm phở bò ngon như hàng quán',
                'content' => 'Mình vừa thử làm phở bò tại nhà và thành công rồi! Chia sẻ với mọi người một số bí quyết: 1. Xương phải ninh ít nhất 6 tiếng để nước dùng ngọt tự nhiên. 2. Gia vị quan trọng nhất là hồi, quế, gừng nướng thơm. 3. Thịt bò nên thái mỏng để khi chan nước dùng nóng sẽ chín vừa tới. Ai có kinh nghiệm gì thêm thì chia sẻ nhé!',
                'image' => 'forum/pho-bo.jpg',
                'views' => 245,
                'is_pinned' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Hỏi cách làm bánh bông lan không bị xẹp',
                'content' => 'Mình làm bánh bông lan mấy lần rồi nhưng cứ bị xẹp sau khi lấy ra khỏi lò. Các bạn có bí quyết gì không? Mình đã thử đánh trứng đủ bông, không mở lò giữa chừng nhưng vẫn bị. Có phải do nhiệt độ lò không đều không nhỉ?',
                'image' => null,
                'views' => 128,
                'is_pinned' => false,
                'status' => 'active',
            ],
            [
                'title' => 'Review công thức gà rán KFC tại nhà',
                'content' => 'Mình vừa thử công thức gà rán kiểu KFC trên trang này và kết quả tuyệt vời! Lớp vỏ giòn rụm, thịt gà mềm ngon. Mẹo là phải ướp gà với sữa chua ít nhất 2 tiếng, và chiên 2 lần để vỏ giòn lâu. Cảm ơn admin đã chia sẻ công thức hay!',
                'image' => 'forum/ga-ran.jpg',
                'recipe_id' => 1,
                'views' => 189,
                'is_pinned' => false,
                'status' => 'active',
            ],
            [
                'title' => 'Chia sẻ cách bảo quản rau củ tươi lâu',
                'content' => 'Mình có một số mẹo bảo quản rau củ muốn chia sẻ: 1. Rau xanh nên rửa sạch, để ráo nước rồi bọc giấy ăn cho vào túi nilon. 2. Cà chua không nên để tủ lạnh vì sẽ mất vị. 3. Khoai tây, hành tỏi để nơi khô ráo, thoáng mát. 4. Nấm tươi nên để trong túi giấy, không nên rửa trước khi bảo quản.',
                'image' => null,
                'views' => 95,
                'is_pinned' => false,
                'status' => 'active',
            ],
            [
                'title' => 'Món ăn vặt cho bé yêu thích nhất',
                'content' => 'Các mẹ cho mình hỏi món ăn vặt nào vừa bổ dưỡng vừa hấp dẫn cho bé 3 tuổi ạ? Con mình hay kén ăn lắm. Mình đã thử làm khoai lang chiên, sữa chua trái cây nhưng bé không thích lắm. Mọi người có gợi ý gì không?',
                'image' => null,
                'views' => 67,
                'is_pinned' => false,
                'status' => 'active',
            ],
            [
                'title' => 'Cách làm nước mắm chấm chuẩn vị miền Nam',
                'content' => 'Nước mắm chấm là linh hồn của nhiều món ăn. Công thức chuẩn: 2 thìa nước mắm, 3 thìa nước, 2 thìa đường, 1 thìa chanh, tỏi ớt băm nhỏ. Mẹo là phải khuấy đường tan hết trước khi cho nước mắm để không bị đắng. Ai thích vị chua thêm có thể cho thêm chanh nhé!',
                'image' => 'forum/nuoc-mam.jpg',
                'views' => 312,
                'is_pinned' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Thắc mắc về cách sử dụng nồi chiên không dầu',
                'content' => 'Mình mới mua nồi chiên không dầu nhưng chưa biết dùng sao cho hiệu quả. Có cần phun dầu lên thực phẩm không? Nhiệt độ và thời gian chuẩn cho từng loại thực phẩm là bao nhiêu? Mọi người chia sẻ kinh nghiệm với ạ!',
                'image' => null,
                'views' => 143,
                'is_pinned' => false,
                'status' => 'active',
            ],
            [
                'title' => 'Công thức làm bánh mì Việt Nam giòn ngon',
                'content' => 'Sau nhiều lần thử nghiệm, cuối cùng mình cũng làm được bánh mì giòn tan như ngoài hàng! Bí quyết là phải ủ bột đủ thời gian, phun nước vào lò khi nướng để vỏ giòn. Nhân bánh mì thì tùy thích, mình thích nhất là pate, chả lụa, dưa leo và rau thơm.',
                'image' => 'forum/banh-mi.jpg',
                'views' => 278,
                'is_pinned' => false,
                'status' => 'active',
            ],
        ];

        foreach ($posts as $post) {
            $post['user_id'] = $userIds[array_rand($userIds)];
            DB::table('forum_posts')->insert($post);
        }

        $this->command->info('Đã tạo ' . count($posts) . ' bài viết diễn đàn với user ngẫu nhiên.');
    }
}
