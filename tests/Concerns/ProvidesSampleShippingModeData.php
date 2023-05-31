<?php

namespace DV5150\Shop\Tests\Concerns;

trait ProvidesSampleShippingModeData
{
    public array $expectedShippingModeData;
    public array $expectedShippingModeOrderItemData;

    public function setUpSampleShippingModeData()
    {
        $this->expectedShippingModeData = [
            'provider' => 'default',
            'name' => 'TEST SHIPPING MODE',
            'priceGross' => 490.0,
        ];

        $this->expectedShippingModeOrderItemData = [
            'name' => 'TEST SHIPPING MODE',
            'quantity' => 1,
            'price_gross' => 490.0,
            'info' => '[SHIPPING]',
        ];
    }
}
