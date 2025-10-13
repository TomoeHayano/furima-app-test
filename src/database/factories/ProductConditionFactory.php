<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductCondition;

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
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
