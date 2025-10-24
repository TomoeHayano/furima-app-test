<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductConditionSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('product_conditions')->insert([
      ['id' => 1, 'status_name' => '良好'],
      ['id' => 2, 'status_name' => '目立った傷や汚れなし'],
      ['id' => 3, 'status_name' => 'やや傷や汚れあり'],
      ['id' => 4, 'status_name' => '状態が悪い'],
    ]);
  }
}
