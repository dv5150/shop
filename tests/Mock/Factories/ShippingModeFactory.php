<?php

namespace DV5150\Shop\Tests\Mock\Factories;

use DV5150\Shop\Tests\Mock\Models\ShippingMode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShippingModeFactory extends Factory
{
    protected $model = ShippingMode::class;

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
            'price_gross' => 490.0,
            'component_name' => Str::of($name)->studly(),
        ];
    }
}
