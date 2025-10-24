<?php

namespace Database\Factories;

use App\Models\ProductCondition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductConditionFactory extends Factory
{
  protected $model = ProductCondition::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'status_name' => $this->faker->unique()->lexify('condition_????'),
      'created_at'  => now(),
      'updated_at'  => now(),
    ];
  }
}
