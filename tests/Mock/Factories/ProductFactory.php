<?php

namespace DV5150\Shop\Tests\Mock\Factories;

use DV5150\Shop\Tests\Mock\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->company() . ' ' . Arr::random([
            'Sweater', 'Pants', 'Shirt', 'Glasses', 'Hat', 'Socks'
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->realText(255),
            'price_gross' => $this->faker->numberBetween(1, 70) * 1000 - 10,
            'is_digital_item' => false,
        ];
    }

    public function digital(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_digital_item' => true,
            ];
        });
    }
}
