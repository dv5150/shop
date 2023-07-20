<?php

namespace DV5150\Shop\Tests\Mock\Factories\Discounts;

use DV5150\Shop\Tests\Mock\Models\Deals\Discounts\ProductValueDiscount;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductValueDiscountFactory extends Factory
{
    protected $model = ProductValueDiscount::class;

    public function definition()
    {
        return [
            'name' => $this->faker->realText(100),
            'value' => $this->faker->numberBetween(1, 5) * 1000 - 10,
        ];
    }
}
