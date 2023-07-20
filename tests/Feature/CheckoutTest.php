<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Contracts\Models\OrderContract;
use DV5150\Shop\Contracts\Models\PaymentModeContract;
use DV5150\Shop\Contracts\Models\ShippingModeContract;
use DV5150\Shop\Facades\Shop;
use DV5150\Shop\Tests\Mock\PaymentProviders\TestPaymentProvider;
use function Pest\Laravel\be;
use function Pest\Laravel\post;

it('can store one item in an order as a guest', function (array $orderData) {
    $productA = $this->productClass::factory()->create();

    /** @var ShippingModeContract $shippingMode */
    $shippingMode = config('shop.models.shippingMode')::factory()
        ->create();

    $shippingMode
        ->paymentModes()
        ->sync(config('shop.models.paymentMode')::factory()->create());

    post(route('api.shop.checkout.store'), array_merge($orderData['source'], [
        'cartData' => [
            [
                'item' => ['id' => $productA->getKey()],
                'quantity' => 2,
            ],
        ],
        'shippingMode' => [
            'provider' => $shippingMode->getProvider(),
        ],
        'paymentMode' => [
            'provider' => $shippingMode->paymentModes()->first()->getProvider(),
        ],
    ]));

    $this->assertDatabaseHas('orders', $orderData['expected']);
    $this->assertDatabaseCount('orders', 1);

    $order = config('shop.models.order')::first();

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->getKey(),
        'sellable_type' => $productA::class,
        'sellable_id' => $productA->getKey(),
        'name' => $productA->getName(),
        'quantity' => 2,
        'price_gross' => $productA->getPriceGross(),
        'info' => null,
        'type' => 'product',
    ]);
})->with([
    // required fields only
    fn () => [
        'source' => $this->testOrderDataRequired,
        'expected' => array_merge($this->expectedOrderDataRequired, ['user_id' => null]),
    ],
    // full form filled
    fn () => [
        'source' => array_merge_recursive($this->testOrderDataRequired, [
            'personalData' => ['comment' => 'Test comment personal'],
            'shippingData' => ['comment' => 'Test comment shipping'],
            'billingData' => ['taxNumber' => '9000000'],
        ]),
        'expected' => array_merge_recursive($this->expectedOrderDataRequired, [
            'comment' => 'Test comment personal',
            'shipping_comment' => 'Test comment shipping',
            'billing_tax_number' => '9000000',
            'user_id' => null,
        ]),
    ],
]);

it('can store multiple items in an order as a guest', function (array $orderData) {
    list($productA, $productB) = $this->productClass::factory()->count(2)->create()->all();

    /** @var ShippingModeContract $shippingMode */
    $shippingMode = config('shop.models.shippingMode')::factory()
        ->create();

    $shippingMode
        ->paymentModes()
        ->sync(config('shop.models.paymentMode')::factory()->create());

    post(route('api.shop.checkout.store'), array_merge($orderData['source'], [
        'cartData' => [
            [
                'item' => ['id' => $productA->getKey()],
                'quantity' => 2,
            ],
            [
                'item' => ['id' => $productB->getKey()],
                'quantity' => 4,
            ]
        ],
        'shippingMode' => [
            'provider' => $shippingMode->getProvider(),
        ],
        'paymentMode' => [
            'provider' => $shippingMode->paymentModes()->first()->getProvider(),
        ],
    ]));

    $this->assertDatabaseHas('orders', $orderData['expected']);
    $this->assertDatabaseCount('orders', 1);

    $order = config('shop.models.order')::first();

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->getKey(),
        'sellable_type' => $productA::class,
        'sellable_id' => $productA->getKey(),
        'name' => $productA->getName(),
        'quantity' => 2,
        'price_gross' => $productA->getPriceGross(),
        'info' => null,
        'type' => 'product',
    ]);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->getKey(),
        'sellable_type' => $productB::class,
        'sellable_id' => $productB->getKey(),
        'name' => $productB->getName(),
        'quantity' => 4,
        'price_gross' => $productB->getPriceGross(),
        'info' => null,
        'type' => 'product',
    ]);
})->with([
    // required fields only
    fn () => [
        'source' => $this->testOrderDataRequired,
        'expected' => array_merge($this->expectedOrderDataRequired, ['user_id' => null]),
    ],
    // full form filled
    fn () => [
        'source' => array_merge_recursive($this->testOrderDataRequired, [
            'personalData' => ['comment' => 'Test comment personal'],
            'shippingData' => ['comment' => 'Test comment shipping'],
            'billingData' => ['taxNumber' => '9000000'],
        ]),
        'expected' => array_merge_recursive($this->expectedOrderDataRequired, [
            'comment' => 'Test comment personal',
            'shipping_comment' => 'Test comment shipping',
            'billing_tax_number' => '9000000',
            'user_id' => null,
        ]),
    ],
]);

it('can store one item in an order as a user', function (array $data) {
    $productA = $this->productClass::factory()->create();

    /** @var ShippingModeContract $shippingMode */
    $shippingMode = config('shop.models.shippingMode')::factory()
        ->create();

    $shippingMode
        ->paymentModes()
        ->sync(config('shop.models.paymentMode')::factory()->create());

    be($data['user']);

    post(route('api.shop.checkout.store'), array_merge($data['order']['source'], [
        'cartData' => [
            [
                'item' => ['id' => $productA->getKey()],
                'quantity' => 2,
            ],
        ],
        'shippingMode' => [
            'provider' => $shippingMode->getProvider(),
        ],
        'paymentMode' => [
            'provider' => $shippingMode->paymentModes()->first()->getProvider(),
        ],
    ]));

    $this->assertDatabaseHas('orders', array_merge($data['order']['expected'], [
        'user_id' => $data['user']->getKey(),
    ]));

    $this->assertDatabaseCount('orders', 1);

    $order = config('shop.models.order')::first();

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->getKey(),
        'sellable_type' => $productA::class,
        'sellable_id' => $productA->getKey(),
        'name' => $productA->getName(),
        'quantity' => 2,
        'price_gross' => $productA->getPriceGross(),
        'info' => null,
        'type' => 'product',
    ]);
})->with([
    // required fields only
    fn () => [
        'order' => [
            'source' => $this->testOrderDataRequired,
            'expected' => $this->expectedOrderDataRequired,
        ],
        'user' => config('shop.models.user')::factory()->create()
    ],
    // full form filled
    fn () => [
        'order' => [
            'source' => array_merge_recursive($this->testOrderDataRequired, [
                'personalData' => ['comment' => 'Test comment personal'],
                'shippingData' => ['comment' => 'Test comment shipping'],
                'billingData' => ['taxNumber' => '9000000'],
            ]),
            'expected' => array_merge_recursive($this->expectedOrderDataRequired, [
                'comment' => 'Test comment personal',
                'shipping_comment' => 'Test comment shipping',
                'billing_tax_number' => '9000000',
            ]),
        ],
        'user' => config('shop.models.user')::factory()->create()
    ],
]);

it('can store multiple items in an order as a user', function (array $data) {
    list($productA, $productB) = $this->productClass::factory()->count(2)->create()->all();

    /** @var ShippingModeContract $shippingMode */
    $shippingMode = config('shop.models.shippingMode')::factory()
        ->create();

    $shippingMode
        ->paymentModes()
        ->sync(config('shop.models.paymentMode')::factory()->create());

    be($data['user']);

    post(route('api.shop.checkout.store'), array_merge($data['order']['source'], [
        'cartData' => [
            [
                'item' => ['id' => $productA->getKey()],
                'quantity' => 1,
            ],
            [
                'item' => ['id' => $productB->getKey()],
                'quantity' => 6,
            ],
        ],
        'shippingMode' => [
            'provider' => $shippingMode->getProvider(),
        ],
        'paymentMode' => [
            'provider' => $shippingMode->paymentModes()->first()->getProvider(),
        ],
    ]));

    $this->assertDatabaseHas('orders', array_merge($data['order']['expected'], [
        'user_id' => $data['user']->getKey(),
    ]));

    $this->assertDatabaseCount('orders', 1);

    $order = config('shop.models.order')::first();

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->getKey(),
        'sellable_type' => $productA::class,
        'sellable_id' => $productA->getKey(),
        'name' => $productA->getName(),
        'quantity' => 1,
        'price_gross' => $productA->getPriceGross(),
        'info' => null,
        'type' => 'product',
    ]);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->getKey(),
        'sellable_type' => $productB::class,
        'sellable_id' => $productB->getKey(),
        'name' => $productB->getName(),
        'quantity' => 6,
        'price_gross' => $productB->getPriceGross(),
        'info' => null,
        'type' => 'product',
    ]);
})->with([
    // required fields only
    fn () => [
        'order' => [
            'source' => $this->testOrderDataRequired,
            'expected' => $this->expectedOrderDataRequired,
        ],
        'user' => config('shop.models.user')::factory()->create()
    ],
    // full form filled
    fn () => [
        'order' => [
            'source' => array_merge_recursive($this->testOrderDataRequired, [
                'personalData' => ['comment' => 'Test comment personal'],
                'shippingData' => ['comment' => 'Test comment shipping'],
                'billingData' => ['taxNumber' => '9000000'],
            ]),
            'expected' => array_merge_recursive($this->expectedOrderDataRequired, [
                'comment' => 'Test comment personal',
                'shipping_comment' => 'Test comment shipping',
                'billing_tax_number' => '9000000',
            ]),
        ],
        'user' => config('shop.models.user')::factory()->create()
    ],
]);

it('removes sellable relation from order items when the attached item is deleted', function () {
    list($productA, $productB) = $this->productClass::factory()->count(2)->create()->all();

    /** @var ShippingModeContract $shippingMode */
    $shippingMode = config('shop.models.shippingMode')::factory()
        ->create();

    $shippingMode
        ->paymentModes()
        ->sync(config('shop.models.paymentMode')::factory()->create());

    post(route('api.shop.checkout.store'), array_merge($this->testOrderDataRequired, [
        'cartData' => [
            [
                'item' => ['id' => $productA->getKey()],
                'quantity' => 2,
            ],
            [
                'item' => ['id' => $productB->getKey()],
                'quantity' => 4,
            ],
        ],
        'shippingMode' => [
            'provider' => $shippingMode->getProvider(),
        ],
        'paymentMode' => [
            'provider' => $shippingMode->paymentModes()->first()->getProvider(),
        ],
    ]));

    $productB->delete();

    $order = config('shop.models.order')::first();

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->getKey(),
        'sellable_type' => $productA::class,
        'sellable_id' => $productA->getKey(),
        'name' => $productA->getName(),
        'quantity' => 2,
        'price_gross' => $productA->getPriceGross(),
        'info' => null,
        'type' => 'product',
    ]);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->getKey(),
        'sellable_type' => null,
        'sellable_id' => null,
        'name' => $productB->getName(),
        'quantity' => 4,
        'price_gross' => $productB->getPriceGross(),
        'info' => null,
        'type' => 'product',
    ]);
});

it('returns the correct redirect url on saving an order without the frontend package or any online payment modes installed', function () {
    list($productA, $productB) = $this->productClass::factory()->count(2)->create()->all();

    /** @var ShippingModeContract $shippingMode */
    $shippingMode = config('shop.models.shippingMode')::factory()
        ->create();

    $shippingMode
        ->paymentModes()
        ->sync(config('shop.models.paymentMode')::factory()->create());

    $response = post(route('api.shop.checkout.store'), array_merge($this->testOrderDataRequired, [
        'cartData' => [
            [
                'item' => ['id' => $productA->getKey()],
                'quantity' => 2,
            ],
            [
                'item' => ['id' => $productB->getKey()],
                'quantity' => 4,
            ],
        ],
        'shippingMode' => [
            'provider' => $shippingMode->getProvider(),
        ],
        'paymentMode' => [
            'provider' => $shippingMode->paymentModes()->first()->getProvider(),
        ],
    ]));

    /** @var OrderContract $order */
    $order = config('shop.models.order')::first();

    expect(Shop::isFrontendInstalled())
        ->toBeFalse()
        ->and($order->requiresOnlinePayment())
        ->toBeFalse()
        ->and($response->getContent())
        ->toBeJson()
        ->json()
        ->redirectUrl
        ->toBe(route('home'));
});

it('returns the correct redirect url on saving an order with the frontend package installed', function () {
    list($productA, $productB) = $this->productClass::factory()->count(2)->create()->all();

    /** @var ShippingModeContract $shippingMode */
    $shippingMode = config('shop.models.shippingMode')::factory()
        ->create();

    $shippingMode
        ->paymentModes()
        ->sync(config('shop.models.paymentMode')::factory()->create());

    Shop::shouldReceive('isFrontendInstalled')
        ->once()
        ->andReturnTrue();

    $response = post(route('api.shop.checkout.store'), array_merge($this->testOrderDataRequired, [
        'cartData' => [
            [
                'item' => ['id' => $productA->getKey()],
                'quantity' => 2,
            ],
            [
                'item' => ['id' => $productB->getKey()],
                'quantity' => 4,
            ],
        ],
        'shippingMode' => [
            'provider' => $shippingMode->getProvider(),
        ],
        'paymentMode' => [
            'provider' => $shippingMode->paymentModes()->first()->getProvider(),
        ],
    ]));

    /** @var OrderContract $order */
    $order = config('shop.models.order')::first();

    expect($order->requiresOnlinePayment())
        ->toBeFalse()
        ->and($response->getContent())
        ->toBeJson()
        ->json()
        ->redirectUrl
        ->toBe(route('shop.order.thankYou', [
            'order' => $order->getUuid(),
        ]));
});

it('returns the correct redirect url on saving an order with an online payment provider installed', function () {
    list($productA, $productB) = $this->productClass::factory()->count(2)->create()->all();

    Shop::registerPaymentProviders([
        TestPaymentProvider::class,
    ]);

    /** @var ShippingModeContract $shippingMode */
    $shippingMode = config('shop.models.shippingMode')::factory()
        ->create();

    $shippingMode
        ->paymentModes()
        ->sync(config('shop.models.paymentMode')::factory()->online()->create([
            'provider' => 'test',
        ]));

    /** @var PaymentModeContract $paymentMode */
    $paymentMode = config('shop.models.paymentMode')::first();

    expect($paymentMode->getProvider() === 'test')->toBeTrue();

    Shop::shouldReceive('isFrontendInstalled')->never();

    $response = post(route('api.shop.checkout.store'), array_merge($this->testOrderDataRequired, [
        'cartData' => [
            [
                'item' => ['id' => $productA->getKey()],
                'quantity' => 2,
            ],
            [
                'item' => ['id' => $productB->getKey()],
                'quantity' => 4,
            ],
        ],
        'shippingMode' => [
            'provider' => $shippingMode->getProvider(),
        ],
        'paymentMode' => [
            'provider' => $shippingMode->paymentModes()->first()->getProvider(),
        ],
    ]));

    /** @var OrderContract $order */
    $order = config('shop.models.order')::first();

    expect($order->requiresOnlinePayment())
        ->toBeTrue()
        ->and($response->getContent())
        ->toBeJson()
        ->json()
        ->redirectUrl
        ->toBe(route('shop.pay', [
            'paymentProvider' => 'test',
            'order' => $order->getUuid(),
        ]));
});
