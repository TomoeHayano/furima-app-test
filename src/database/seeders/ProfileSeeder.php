<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileSeeder extends Seeder
{
  public function run()
  {
    DB::table('profiles')->insert([
      [
        'user_id'       => 1,
        'postal_code'   => '111-1111',
        'address'       => '福岡県福岡市中央区天神1-1-1',
        'building_name' => 'コーチテックビル301号室',
        'image_path'    => 'products/プロフィール写真.jpeg',
        'created_at'    => now(),
        'updated_at'    => now(),
      ],
      [
        'user_id'       => 2,
        'postal_code'   => '222-2222',
        'address'       => '東京都渋谷区神南2-2-2',
        'building_name' => 'サンプルビル502号室',
        'image_path'    => 'products/プロフィール写真.jpeg',
        'created_at'    => now(),
        'updated_at'    => now(),
      ],
    ]);
  }
}
