<?php

namespace DV5150\Shop\Tests\Concerns;

trait ProvidesSampleOrderData
{
    public array $testOrderData;
    public array $expectedBaseOrderData;

    public array $expectedProductAData;
    public array $expectedProductBData;

    public function setUpSampleOrderData()
    {
        $this->testOrderData = [
            'personalData' => [
                'email' => 'tester+mailaddress+10000@my-webshop.com',
                'phone' => '+36301001000',
            ],
            'shippingData' => [
                'name' => 'Test Name 1000',
                'zipCode' => '1000',
                'city' => 'Budapest 1000',
                'street' => 'One street 1000',
                'comment' => 'There are no comments here 1000',
            ],
            'billingData' => [
                'name' => 'Another Name 9000',
                'zipCode' => '9000',
                'city' => 'Győr 9000',
                'street' => 'Street 9000',
                'taxNumber' => '900000000',
            ],
        ];

        $this->expectedBaseOrderData = [
            'email' => $this->testOrderData['personalData']['email'],
            'phone' => $this->testOrderData['personalData']['phone'],
            'shipping_name' => $this->testOrderData['shippingData']['name'],
            'shipping_zip_code' => $this->testOrderData['shippingData']['zipCode'],
            'shipping_city' => $this->testOrderData['shippingData']['city'],
            'shipping_address' => $this->testOrderData['shippingData']['street'],
            'shipping_comment' => $this->testOrderData['shippingData']['comment'],
            'billing_name' => $this->testOrderData['billingData']['name'],
            'billing_zip_code' => $this->testOrderData['billingData']['zipCode'],
            'billing_city' => $this->testOrderData['billingData']['city'],
            'billing_address' => $this->testOrderData['billingData']['street'],
            'billing_tax_number' => $this->testOrderData['billingData']['taxNumber'],
        ];
    }
}
