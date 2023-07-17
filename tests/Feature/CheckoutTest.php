<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Concerns\Shop\HandlesPaymentProviderRegistration;
use DV5150\Shop\Contracts\Models\OrderContract;
use DV5150\Shop\Contracts\Models\PaymentModeContract;
use DV5150\Shop\Contracts\Services\ShopServiceContract;
use DV5150\Shop\Facades\Shop;
use DV5150\Shop\Models\Default\Order;
use DV5150\Shop\Services\ShopService;
use DV5150\Shop\Tests\Concerns\ProvidesSampleOrderData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleShippingModeData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleUser;
use DV5150\Shop\Tests\Mock\PaymentProviders\TestPaymentProvider;
use DV5150\Shop\Tests\TestCase;
use Illuminate\Support\Facades\App;
use Mockery;

class CheckoutTest extends TestCase
{
    use ProvidesSampleUser,
        ProvidesSampleOrderData,
        ProvidesSampleShippingModeData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpSampleUser();
        $this->setUpSampleOrderData();
        $this->setUpSampleShippingModeData();
    }

    /** @test */
    public function an_order_with_multiple_items_can_be_stored_as_guest()
    {
        $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem(sellableItem: $this->productA, quantity: 2),
                    $this->makeProductCartDataItem(sellableItem: $this->productB, quantity: 4),
                ],
                'shippingMode' => [
                    'provider' => $this->shippingModeProvider,
                ],
                'paymentMode' => [
                    'provider' => $this->paymentModeProvider,
                ],
                'shipping_mode_provider' => $this->shippingModeProvider,
                'payment_mode_provider' => $this->paymentModeProvider,
            ])
        );

        $order = config('shop.models.order')::first();

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => null,
        ]));

        $this->assertDatabaseHasProductOrderItem(sellableItem: $this->productA, order: $order, quantity: 2);
        $this->assertDatabaseHasProductOrderItem(sellableItem: $this->productB, order: $order, quantity: 4);
    }

    /** @test */
    public function an_order_with_multiple_items_can_be_stored_as_user()
    {
        $this->be($this->testUser);

        $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem(sellableItem: $this->productA, quantity: 5),
                    $this->makeProductCartDataItem(sellableItem: $this->productB, quantity: 3),
                ],
                'shippingMode' => [
                    'provider' => $this->shippingModeProvider,
                ],
                'paymentMode' => [
                    'provider' => $this->paymentModeProvider,
                ],
                'shipping_mode_provider' => $this->shippingModeProvider,
                'payment_mode_provider' => $this->paymentModeProvider,
            ])
        );

        $order = config('shop.models.order')::first();

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => $this->testUser->getKey(),
        ]));

        $this->assertDatabaseHasProductOrderItem(sellableItem: $this->productA, order: $order, quantity: 5);
        $this->assertDatabaseHasProductOrderItem(sellableItem: $this->productB, order: $order, quantity: 3);
    }

    /** @test */
    public function an_order_with_a_single_item_can_be_stored_as_guest()
    {
        $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem(sellableItem: $this->productA),
                ],
                'shippingMode' => [
                    'provider' => $this->shippingModeProvider,
                ],
                'paymentMode' => [
                    'provider' => $this->paymentModeProvider,
                ],
                'shipping_mode_provider' => $this->shippingModeProvider,
                'payment_mode_provider' => $this->paymentModeProvider,
            ])
        );

        $order = config('shop.models.order')::first();

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => null,
        ]));

        $this->assertDatabaseHasProductOrderItem(sellableItem: $this->productA, order: $order);
    }

    /** @test */
    public function an_order_with_a_single_item_can_be_stored_as_user()
    {
        $this->be($this->testUser);

        $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem(sellableItem: $this->productB, quantity: 2),
                ],
                'shippingMode' => [
                    'provider' => $this->shippingModeProvider,
                ],
                'paymentMode' => [
                    'provider' => $this->paymentModeProvider,
                ],
                'shipping_mode_provider' => $this->shippingModeProvider,
                'payment_mode_provider' => $this->paymentModeProvider,
            ])
        );

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => $this->testUser->getKey(),
        ]));

        $order = config('shop.models.order')::first();

        $this->assertDatabaseHasProductOrderItem(sellableItem: $this->productB, order: $order, quantity: 2);
    }

    /** @test */
    public function order_items_lose_their_sellable_relation_when_the_attached_item_is_deleted()
    {
        $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem(sellableItem: $this->productA, quantity: 2),
                    $this->makeProductCartDataItem(sellableItem: $this->productB, quantity: 4),
                ],
                'shippingMode' => [
                    'provider' => $this->shippingModeProvider,
                ],
                'paymentMode' => [
                    'provider' => $this->paymentModeProvider,
                ],
                'shipping_mode_provider' => $this->shippingModeProvider,
                'payment_mode_provider' => $this->paymentModeProvider,
            ])
        );

        $this->productB->delete();

        $order = config('shop.models.order')::first();

        $this->assertDatabaseHasProductOrderItem(
            sellableItem: $this->productA,
            order: $order,
            quantity: 2
        );

        $this->assertDatabaseHasProductOrderItemWithMissingRelation(
            sellableItem: $this->productB,
            order: $order,
            quantity: 4
        );
    }

    /** @test */
    public function saving_and_order_returns_the_correct_redirect_url_without_online_payment_or_frontend_installed()
    {
        $this->assertFalse(Shop::isFrontendInstalled());

        $response = $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem(sellableItem: $this->productA, quantity: 2),
                    $this->makeProductCartDataItem(sellableItem: $this->productB, quantity: 4),
                ],
                'shippingMode' => [
                    'provider' => $this->shippingModeProvider,
                ],
                'paymentMode' => [
                    'provider' => $this->paymentModeProvider,
                ],
                'shipping_mode_provider' => $this->shippingModeProvider,
                'payment_mode_provider' => $this->paymentModeProvider,
            ])
        );

        /** @var OrderContract $order */
        $order = config('shop.models.order')::first();

        $this->assertFalse($order->requiresOnlinePayment());

        $response->assertJson([
            'redirectUrl' => route('home'),
        ]);
    }

    /** @test */
    public function saving_and_order_returns_the_correct_redirect_url_with_frontend_installed()
    {
        $this->instance(ShopServiceContract::class, $this->getTestShopService());

        $this->assertTrue(Shop::isFrontendInstalled());

        $response = $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem(sellableItem: $this->productA, quantity: 2),
                    $this->makeProductCartDataItem(sellableItem: $this->productB, quantity: 4),
                ],
                'shippingMode' => [
                    'provider' => $this->shippingModeProvider,
                ],
                'paymentMode' => [
                    'provider' => $this->paymentModeProvider,
                ],
                'shipping_mode_provider' => $this->shippingModeProvider,
                'payment_mode_provider' => $this->paymentModeProvider,
            ])
        );

        /** @var OrderContract $order */
        $order = config('shop.models.order')::first();

        $this->assertFalse($order->requiresOnlinePayment());

        $response->assertJson([
            'redirectUrl' => route('shop.order.thankYou', [
                'order' => $order->getUuid(),
            ]),
        ]);
    }

    /** @test */
    public function saving_and_order_returns_the_correct_redirect_url_with_online_payment_provider_installed()
    {
        $this->instance(ShopServiceContract::class, $this->getTestShopService());

        $this->assertTrue(Shop::isFrontendInstalled());

        Shop::registerPaymentProviders([
            TestPaymentProvider::class,
        ]);

        /** @var PaymentModeContract $paymentMode */
        $paymentMode = config('shop.models.paymentMode')::first();

        $this->assertTrue($paymentMode->getProvider() === 'test');

        $paymentMode->update(['is_online_payment' => true]);

        $response = $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem(sellableItem: $this->productA, quantity: 2),
                    $this->makeProductCartDataItem(sellableItem: $this->productB, quantity: 4),
                ],
                'shippingMode' => [
                    'provider' => $this->shippingModeProvider,
                ],
                'paymentMode' => [
                    'provider' => $this->paymentModeProvider,
                ],
                'shipping_mode_provider' => $this->shippingModeProvider,
                'payment_mode_provider' => $this->paymentModeProvider,
            ])
        );

        /** @var OrderContract $order */
        $order = config('shop.models.order')::first();

        $this->assertTrue($order->requiresOnlinePayment());

        $response->assertJson([
            'redirectUrl' => route('pay', [
                'paymentProvider' => 'test',
                'order' => $order->getUuid(),
            ]),
        ]);
    }

    protected function getTestShopService(): object
    {
        return new class {
            use HandlesPaymentProviderRegistration;

            public static function isFrontendInstalled(): bool
            {
                return true;
            }
        };
    }
}
