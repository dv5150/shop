<?php

namespace DV5150\Shop\Tests\Unit\Models;

use DV5150\Shop\Contracts\PaymentModeContract;
use DV5150\Shop\Tests\Concerns\ProvidesSampleShippingModeData;
use DV5150\Shop\Tests\TestCase;

class PaymentModeTest extends TestCase
{
    use ProvidesSampleShippingModeData;

    protected PaymentModeContract $paymentMode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpSampleShippingModeData();

        $this->paymentMode = $this->shippingMode
            ->paymentModes()
            ->first();
    }

    /** @test */
    public function payment_mode_has_a_fixed_prefix()
    {
        $this->assertSame('[PAYMENT]', $this->paymentMode->getShortName());
    }

    /** @test */
    public function payment_mode_can_be_converted_to_order_item()
    {
        $expected = new (config('shop.models.orderItem'))([
            'name' => $this->paymentMode->getName(),
            'quantity' => 1,
            'price_gross' => $this->paymentMode->getPriceGross(),
            'info' => $this->paymentMode->getShortName(),
        ]);

        $actual = $this->paymentMode->toOrderItem();

        $this->assertTrue($expected->is($actual));
    }
}