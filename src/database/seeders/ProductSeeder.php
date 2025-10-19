<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ProductSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            [
                'user_id' => 1, // 仮ユーザーID（usersに1が存在する前提）
                'name' => '腕時計',
                'brand_name' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => 15000,
                'condition_id' => 1, // 「良好」に対応するID
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'is_sold' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'HDD',
                'brand_name' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'price' => 5000,
                'condition_id' => 2, // 「目立った傷や汚れなし」
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'is_sold' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => '玉ねぎ3束',
                'brand_name' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'price' => 300,
                'condition_id' => 3, // 「やや傷や汚れあり」
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'is_sold' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => '革靴',
                'brand_name' => null,
                'description' => 'クラシックなデザインの革靴',
                'price' => 4000,
                'condition_id' => 4, // 「状態が悪い」
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'is_sold' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'ノートPC',
                'brand_name' => null,
                'description' => '高性能なノートパソコン',
                'price' => 45000,
                'condition_id' => 1,
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'is_sold' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'name' => 'マイク',
                'brand_name' => null,
                'description' => '高音質のレコーディング用マイク',
                'price' => 8000,
                'condition_id' => 2,
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'is_sold' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'ショルダーバッグ',
                'brand_name' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'price' => 3500,
                'condition_id' => 3,
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'is_sold' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'name' => 'タンブラー',
                'brand_name' => null,
                'description' => '使いやすいタンブラー',
                'price' => 500,
                'condition_id' => 4,
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'is_sold' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'name' => 'コーヒーミル',
                'brand_name' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'price' => 4000,
                'condition_id' => 1,
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'is_sold' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'メイクセット',
                'brand_name' => null,
                'description' => '便利なメイクアップセット',
                'price' => 2500,
                'condition_id' => 2,
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'is_sold' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
