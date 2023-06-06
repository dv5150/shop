<?php

namespace DV5150\Shop\Tests\Concerns;

trait ProvidesSamplePaymentModeData
{
    public array $paymentModeData;
    public array $expectedPaymentModeData;
    public array $expectedPaymentModeOrderItemData;

    public function setUpSamplePaymentModeData()
    {
        $this->paymentModeData = [
            'provider' => 'default',
            'name' => 'TEST SHIPPING MODE',
            'price_gross' => 550.0,
        ];

        $this->expectedPaymentModeData = [
            'provider' => 'default',
            'name' => 'TEST SHIPPING MODE',
            'priceGross' => 550.0,
        ];

        $this->expectedPaymentModeOrderItemData = [
            'name' => 'TEST PAYMENT MODE',
            'quantity' => 1,
            'price_gross' => 550.0,
            'info' => '[PAYMENT]',
        ];
    }
}
