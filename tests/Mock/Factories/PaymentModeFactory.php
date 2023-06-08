<?php

namespace DV5150\Shop\Tests\Mock\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $name = $this->faker->words(5, true);

        return [
            'name' => $name,
            'provider' => Str::of($name)->replace(' ', '')->lower(),
            'price_gross' => 190.0,
        ];
    }
}
