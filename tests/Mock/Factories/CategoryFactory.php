<?php

namespace DV5150\Shop\Tests\Mock\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class CategoryFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = trim(Arr::first(explode(' ', $this->faker->jobTitle())));

        return [
            'name' => $name,
        ];
    }
}
