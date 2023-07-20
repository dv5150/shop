<?php

namespace DV5150\Shop\Tests\Mock\Factories\Coupons;

use DV5150\Shop\Tests\Mock\Models\Deals\Coupons\CartPercentCoupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartPercentCouponFactory extends Factory
{
    protected $model = CartPercentCoupon::class;

    public function definition()
    {
        return [
            'name' => $this->faker->realText(100),
            'value' => $this->faker->numberBetween(1, 99),
        ];
    }
}
