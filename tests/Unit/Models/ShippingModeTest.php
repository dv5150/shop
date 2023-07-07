<?php

namespace DV5150\Shop\Tests\Unit\Models;

use DV5150\Shop\Tests\Concerns\ProvidesSampleShippingModeData;
use DV5150\Shop\Tests\TestCase;

class ShippingModeTest extends TestCase
{
    use ProvidesSampleShippingModeData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpSampleShippingModeData();
    }

    /** @test */
    public function shipping_mode_can_be_converted_to_order_item()
    {
        $expected = new (config('shop.models.orderItem'))([
            'name' => $this->shippingMode->getName(),
            'quantity' => 1,
            'price_gross' => $this->shippingMode->getPriceGross(),
            'info' => null,
        ]);

        $actual = $this->shippingMode->toOrderItem();

        $this->assertTrue($expected->is($actual));
    }
}