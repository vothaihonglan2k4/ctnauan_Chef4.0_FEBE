<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recipes = [
            [
                'title' => 'Bún Đậu Mắm Tôm',
                'description' => 'Món ăn đặc sản Hà Nội với đậu phụ rán giòn, bún lá, thịt chân giò và mắm tôm pha chanh ớt.',
                'ingredients' => "- 500g Bún lá\n- 3 bìa Đậu phụ\n- 300g Thịt chân giò\n- Mắm tôm, chanh, đường, ớt\n- Rau thơm: Kinh giới, tía tô",
                'instructions' => "1. Luộc thịt chân giò, thái lát mỏng.\n2. Rán đậu phụ vàng giòn các mặt.\n3. Pha mắm tôm với đường, chanh, ớt, đánh sủi bọt.\n4. Trình bày bún, đậu, thịt và rau thơm ra mẹt.",
                'image' => 'recipes/bun_dau.jpg',
                'category_id' => 1,
                'user_id' => 1,
                'status' => 'approved',
            ],
            [
                'title' => 'Sushi & Sashimi Nhật Bản',
                'description' => 'Sự kết hợp tinh tế giữa hải sản tươi sống và cơm trộn giấm.',
                'ingredients' => "- Cơm dẻo Nhật\n- Cá hồi, cá ngừ tươi\n- Rong biển (Nori)\n- Giấm Nhật, đường, muối\n- Wasabi, gừng hồng",
                'instructions' => "1. Nấu cơm và trộn với hỗn hợp giấm.\n2. Thái cá hồi, cá ngừ thành lát mỏng.\n3. Cuộn cơm với rong biển và nhân cá.\n4. Thưởng thức cùng nước tương và wasabi.",
                'image' => 'recipes/sushi.jpg',
                'category_id' => 2,
                'user_id' => 2,
                'status' => 'approved',
            ],
            [
                'title' => 'Tiramisu Ý',
                'description' => 'Món tráng miệng nổi tiếng của Ý với hương vị cà phê và phô mai mascarpone.',
                'ingredients' => "- 250g Phô mai Mascarpone\n- 2 quả Trứng gà\n- 50g Đường\n- 1 gói Bánh sâm panh (Ladyfingers)\n- 1 tách Cà phê đen đậm đặc\n- Bột cacao",
                'instructions' => "1. Đánh bông lòng đỏ trứng với đường, trộn cùng mascarpone.\n2. Nhúng bánh sâm panh nhanh qua cà phê.\n3. Xếp một lớp bánh, sau đó là một lớp kem mascarpone.\n4. Lặp lại và phủ bột cacao lên trên cùng. Để lạnh 4-6 tiếng.",
                'image' => 'recipes/tiramisu.jpg',
                'category_id' => 3,
                'user_id' => 3,
                'status' => 'approved',
            ],
            [
                'title' => 'Salad Hoa Quả Kiểu Hàn',
                'description' => 'Món salad thanh mát, giải ngấy với nhiều loại trái cây tươi.',
                'ingredients' => "- Táo, lê, dưa chuột\n- Ngô ngọt\n- Sốt mayonnaise\n- Nước cốt chanh\n- Mật ong",
                'instructions' => "1. Rửa sạch và thái nhỏ các loại hoa quả.\n2. Trộn sốt mayonnaise với nước chanh và mật ong.\n3. Cho hoa quả vào tô lớn, rưới sốt lên và trộn đều.\n4. Giữ lạnh trước khi ăn.",
                'image' => 'recipes/salad.jpg',
                'category_id' => 2,
                'user_id' => 4,
                'status' => 'approved',
            ],
            [
                'title' => 'Món Ngon Tổng Hợp',
                'description' => 'Công thức nấu các món ngon mỗi ngày cho gia đình.',
                'ingredients' => "- Nguyên liệu đa dạng tùy món\n- Gia vị cơ bản: mắm, muối, hạt nêm",
                'instructions' => "1. Sơ chế nguyên liệu sạch sẽ.\n2. Chế biến theo phương pháp xào, nấu, kho.\n3. Nêm nếm vừa miệng.\n4. Trình bày đẹp mắt.",
                'image' => 'recipes/mon_ngon.jpg',
                'category_id' => 1,
                'user_id' => 1,
                'status' => 'approved',
            ],
        ];

        foreach ($recipes as $recipe) {
            DB::table('recipes')->insert(array_merge($recipe, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
