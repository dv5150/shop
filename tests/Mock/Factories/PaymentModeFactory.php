<?php

namespace DV5150\Shop\Tests\Mock\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentModeFactory extends Factory
{
    public function modelName(): string
    {
        return config('shop.models.paymentMode');
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'test',
            'provider' => 'test',
            'price_gross' => 190.0,
            'is_online_payment' => false,
        ];
    }
}
