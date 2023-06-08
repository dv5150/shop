<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Contracts\PaymentModeContract;
use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Tests\Concerns\ProvidesSampleOrderData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleShippingModeData;
use DV5150\Shop\Tests\TestCase;

class ShippingModeTest extends TestCase
{
    use ProvidesSampleOrderData,
        ProvidesSampleShippingModeData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpSampleOrderData();
        $this->setUpSampleShippingModeData();
    }

    /** @test */
    public function shipping_mode_has_its_attached_payment_modes_aswell()
    {
        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'shippingMode' => null,
                ],
            ]);

        $this->post(route('api.shop.cart.shippingMode.store', [
            'provider' => $this->shippingModeProvider,
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'shippingMode' => [
                        'provider' => $this->shippingModeProvider,
                        'name' => $this->shippingMode->getName(),
                        'priceGross' => $this->shippingMode->getPriceGross(),
                        'componentName' => $this->shippingMode->getComponentName(),
                        'paymentModes' => $this->shippingMode
                            ->paymentModes
                            ->map(fn (PaymentModeContract $paymentMode) => [
                                'provider' => $paymentMode->getProvider(),
                                'name' => $paymentMode->getName(),
                                'priceGross' => $paymentMode->getPriceGross(),
                            ])->all()
                    ],
                ],
            ]);
    }
}
