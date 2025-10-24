<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCondition;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
  protected $model = Product::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'user_id'      => User::factory(), // 出品者（usersテーブルと紐づく）
      'name'         => $this->faker->words(2, true), // 例: "スニーカー Nike"
      'brand_name'   => $this->faker->company(), // 例: "NIKE"
      'description'  => $this->faker->sentence(10), // 説明文
      'price'        => $this->faker->numberBetween(1000, 10000),
      'condition_id' => ProductCondition::factory(), // 商品状態を同時に生成
      'image_path'   => 'default.jpg', // 画像パス
      'is_sold'      => 0, // 初期状態：未販売
      'created_at'   => now(),
      'updated_at'   => now(),
    ];
  }
}
