<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Tests\Concerns\ProvidesSampleOrderData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleProductData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleShippingModeData;
use DV5150\Shop\Tests\TestCase;

class ShippingModeTest extends TestCase
{
    use ProvidesSampleOrderData,
        ProvidesSampleProductData,
        ProvidesSampleShippingModeData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpSampleOrderData();
        $this->setUpSampleProductData();
        $this->setUpSampleShippingModeData();
    }

    /** @test */
    public function default_shipping_mode_can_be_overwritten()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);
        Cart::addItem($this->productC);

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'shippingMode' => $this->expectedShippingModeData,
                ],
            ]);

        config('shop.models.shippingMode')::updateOrCreate([
            'provider' => 'default',
            'name' => 'TEST SHIPPING MODE',
            'price_gross' => 490.0,
            'component_name' => 'TestShippingMode'
        ]);

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'shippingMode' => [
                        'provider' => 'default',
                        'name' => 'TEST SHIPPING MODE',
                        'priceGross' => 490.0,
                        'componentName' => 'TestShippingMode'
                    ],
                ],
            ]);
    }
}
