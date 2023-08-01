<?php

namespace DV5150\Shop\Tests\Mock\Factories;

use DV5150\Shop\Tests\Mock\Models\PaymentMode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentModeFactory extends Factory
{
    protected $model = PaymentMode::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => Str::random(),
            'provider' => Str::random(),
            'price_gross' => 190.0,
            'is_online_payment' => false,
        ];
    }

    public function online(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_online_payment' => true,
            ];
        });
    }
}
