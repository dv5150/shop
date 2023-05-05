<?php

namespace DV5150\Shop\Tests\Mock\Factories;

use DV5150\Shop\Tests\Mock\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
        $suffixes = ['Sweater', 'Pants', 'Shirt', 'Glasses', 'Hat', 'Socks'];

        $name = $this->faker->company() . ' ' . Arr::random($suffixes);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->realText(320),
            'price_gross' => $this->faker->numberBetween(1, 70) * 1000 - 10,
        ];
    }
}
