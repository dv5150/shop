<?php

namespace DV5150\Shop\Tests\Unit\Services;

use DV5150\Shop\Facades\Shop;
use DV5150\Shop\Tests\Mock\PaymentProviders\TestPaymentProvider;
use DV5150\Shop\Tests\TestCase;

class ShopServiceTest extends TestCase
{
    /** @test */
    public function a_payment_provider_can_be_registered()
    {
        $this->assertSame(Shop::getAllPaymentProviders(), []);

        Shop::registerPaymentProviders([
            TestPaymentProvider::class
        ]);

        $this->assertSame(Shop::getAllPaymentProviders(), [
            'test' => TestPaymentProvider::class
        ]);

        $this->assertSame(Shop::getPaymentProvider('test'), TestPaymentProvider::class);
    }
}