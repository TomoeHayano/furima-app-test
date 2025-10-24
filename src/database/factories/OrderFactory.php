<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
  protected $model = Order::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'user_id'                => User::factory(),
      'product_id'             => Product::factory(),
      'profile_id'             => Profile::factory(),
      'payment_method'         => $this->faker->randomElement(['credit', 'cash', 'bank_transfer']),
      'shipping_postal_code'   => $this->faker->regexify('[0-9]{3}-[0-9]{4}'),
      'shipping_address'       => $this->faker->address(),
      'shipping_building_name' => $this->faker->optional()->secondaryAddress(),
      'created_at'             => now(),
      'updated_at'             => now(),
    ];
  }
}
