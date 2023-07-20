<?php

namespace DV5150\Shop\Tests\Mock\Factories\Discounts;

use DV5150\Shop\Tests\Mock\Models\Deals\Discounts\ProductPercentDiscount;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductPercentDiscountFactory extends Factory
{
    protected $model = ProductPercentDiscount::class;

    public function definition()
    {
        return [
            'name' => $this->faker->realText(100),
            'value' => $this->faker->numberBetween(1, 99),
        ];
    }
}
