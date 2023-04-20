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
        $suffixes = [
            'en' => ['Sweater', 'Pants', 'Shirt', 'Glasses', 'Hat', 'Socks'],
            'hu' => ['Pulóver', 'Nadrág', 'Ing', 'Szemüveg', 'Sapka', 'Zokni'],
        ];

        $nameEn = $this->faker->company() . ' ' . Arr::random($suffixes['en']);
        $nameHu = $this->faker->company() . ' ' . Arr::random($suffixes['hu']);

        return [
            'name' => [
                'en' => $nameEn,
                'hu' => $nameHu,
            ],
            'slug' => [
                'en' => Str::slug($nameEn),
                'hu' => Str::slug($nameHu),
            ],
            'description' => [
                'en' => $this->faker->realText(320),
                'hu' => $this->faker->realText(320),
            ],
            'price_gross' => $this->faker->numberBetween(1, 70) * 1000 - 10,
        ];
    }
}
