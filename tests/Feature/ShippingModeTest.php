<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Contracts\ShippingModeContract;
use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Tests\Concerns\ProvidesSampleOrderData;
use DV5150\Shop\Tests\Concerns\ProvidesSamplePaymentModeData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleProductData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleShippingModeData;
use DV5150\Shop\Tests\TestCase;

class ShippingModeTest extends TestCase
{
    use ProvidesSampleOrderData,
        ProvidesSampleProductData,
        ProvidesSampleShippingModeData,
        ProvidesSamplePaymentModeData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpSampleOrderData();
        $this->setUpSampleProductData();
        $this->setUpSampleShippingModeData();
        $this->setUpSamplePaymentModeData();
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

    /** @test */
    public function shipping_mode_has_its_attached_payment_modes_aswell()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);
        Cart::addItem($this->productC);

        $defaultShippingMode = config('shop.models.shippingMode')::create([
            'provider' => 'default',
            'name' => 'TEST SHIPPING MODE',
            'price_gross' => 490.0,
        ]);

        $defaultShippingMode->paymentModes()
            ->createMany([
                [
                    'provider' => 'testpm1',
                    'name' => 'Test Payment Mode 1',
                    'price_gross' => 100.0,
                ],
                [
                    'provider' => 'testpm2',
                    'name' => 'Test Payment Mode 2',
                    'price_gross' => 200.0,
                ],
            ]);

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'shippingMode' => array_merge($this->expectedShippingModeData, [
                        'paymentModes' => [
                            [
                                'provider' => 'testpm1',
                                'name' => 'Test Payment Mode 1',
                                'priceGross' => 100.0,
                            ],
                            [
                                'provider' => 'testpm2',
                                'name' => 'Test Payment Mode 2',
                                'priceGross' => 200.0,
                            ],
                        ]
                    ]),
                ],
            ]);
    }
}
