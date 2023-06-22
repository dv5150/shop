<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Contracts\Models\PaymentModeContract;
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

    /** @test */
    public function payment_mode_not_attached_to_shipping_mode_cannot_be_selected()
    {
        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'shippingMode' => null,
                    'paymentMode' => null,
                ],
            ]);

        $shippingModes = [
            [
                'name' => 'ABC',
                'price_gross' => 1.0,
                'provider' => 'abc',
            ],
            [
                'name' => 'DEF',
                'price_gross' => 1.0,
                'provider' => 'def',
            ],
        ];

        foreach ($shippingModes as $shippingMode) {
            config('shop.models.shippingMode')::create($shippingMode);
        }

        config('shop.models.shippingMode')::firstWhere('provider', 'abc')
            ->paymentModes()
            ->saveMany([
                new (config('shop.models.paymentMode'))([
                    'name' => 'IJK',
                    'price_gross' => 1.0,
                    'provider' => 'ijk',
                ]),
                new (config('shop.models.paymentMode'))([
                    'name' => 'LMN',
                    'price_gross' => 1.0,
                    'provider' => 'lmn',
                ]),
            ]);

        config('shop.models.shippingMode')::firstWhere('provider', 'def')
            ->paymentModes()
            ->saveMany([
                new (config('shop.models.paymentMode'))([
                    'name' => 'OPQ',
                    'price_gross' => 1.0,
                    'provider' => 'opq',
                ]),
                new (config('shop.models.paymentMode'))([
                    'name' => 'RST',
                    'price_gross' => 1.0,
                    'provider' => 'rst',
                ]),
            ]);

        // shipping mode 1

        $this->post(route('api.shop.cart.shippingMode.store', [
            'provider' => 'abc',
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'shippingMode' => [
                        'paymentModes' => [
                            [
                                'provider' => 'ijk',
                                'name' => 'IJK',
                                'priceGross' => 1.0,
                            ],
                            [
                                'provider' => 'lmn',
                                'name' => 'LMN',
                                'priceGross' => 1.0,
                            ]
                        ]
                    ],
                ],
            ]);

        $this->post(route('api.shop.cart.paymentMode.store', [
            'provider' => 'ijk',
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'paymentMode' => [
                        'provider' => 'ijk',
                        'name' => 'IJK',
                        'priceGross' => 1.0,
                    ],
                ],
            ]);

        $this->post(route('api.shop.cart.paymentMode.store', [
            'provider' => 'lmn',
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'paymentMode' => [
                        'provider' => 'lmn',
                        'name' => 'LMN',
                        'priceGross' => 1.0,
                    ],
                ],
            ]);

        $this->post(route('api.shop.cart.paymentMode.store', [
            'provider' => 'opq',
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'paymentMode' => null,
                ],
            ]);

        $this->post(route('api.shop.cart.paymentMode.store', [
            'provider' => 'rst',
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'paymentMode' => null,
                ],
            ]);

        // shipping mode 2

        $this->post(route('api.shop.cart.shippingMode.store', [
            'provider' => 'def',
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'shippingMode' => [
                        'paymentModes' => [
                            [
                                'provider' => 'opq',
                                'name' => 'OPQ',
                                'priceGross' => 1.0,
                            ],
                            [
                                'provider' => 'rst',
                                'name' => 'RST',
                                'priceGross' => 1.0,
                            ]
                        ]
                    ],
                ],
            ]);

        $this->post(route('api.shop.cart.paymentMode.store', [
            'provider' => 'ijk',
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'paymentMode' => null,
                ],
            ]);

        $this->post(route('api.shop.cart.paymentMode.store', [
            'provider' => 'lmn',
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'paymentMode' => null,
                ],
            ]);

        $this->post(route('api.shop.cart.paymentMode.store', [
            'provider' => 'opq',
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'paymentMode' => [
                        'provider' => 'opq',
                        'name' => 'OPQ',
                        'priceGross' => 1.0,
                    ],
                ],
            ]);

        $this->post(route('api.shop.cart.paymentMode.store', [
            'provider' => 'rst',
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'paymentMode' => [
                        'provider' => 'rst',
                        'name' => 'RST',
                        'priceGross' => 1.0,
                    ],
                ],
            ]);
    }
}
