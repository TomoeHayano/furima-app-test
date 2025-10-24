<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
  protected $model = Profile::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'user_id'       => User::factory(),
      'postal_code'   => $this->faker->regexify('[0-9]{3}-[0-9]{4}'),
      'address'       => $this->faker->address(),
      'building_name' => $this->faker->optional()->secondaryAddress(),
      'image_path'    => null,
      'created_at'    => now(),
      'updated_at'    => now(),
    ];
  }
}
