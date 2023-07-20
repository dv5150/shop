<?php

namespace DV5150\Shop\Tests\Mock\Factories\Coupons;

use DV5150\Shop\Tests\Mock\Models\Deals\Coupons\CartValueCoupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartValueCouponFactory extends Factory
{
    protected $model = CartValueCoupon::class;

    public function definition()
    {
        return [
            'name' => $this->faker->realText(100),
            'value' => $this->faker->numberBetween(1, 5) * 1000 - 10,
        ];
    }
}
