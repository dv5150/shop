<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Contracts\Models\OrderContract;
use DV5150\Shop\Tests\Concerns\ProvidesSampleOrderData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleShippingModeData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleUser;
use DV5150\Shop\Tests\TestCase;
use Illuminate\Testing\TestResponse;

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
        $response = $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem($this->productA, 2),
                    $this->makeProductCartDataItem($this->productB, 4),
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

        $this->assertDatabaseHasProductOrderItem($this->productA, $order, 2);
        $this->assertDatabaseHasProductOrderItem($this->productB, $order, 4);

        $this->checkThankYouPageAccessWithOrderAvailable($response, $order);
    }

    /** @test */
    public function an_order_with_multiple_items_can_be_stored_as_user()
    {
        $this->be($this->testUser);

        $response = $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem($this->productA, 5),
                    $this->makeProductCartDataItem($this->productB, 3),
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

        $this->assertDatabaseHasProductOrderItem($this->productA, $order, 5);
        $this->assertDatabaseHasProductOrderItem($this->productB, $order, 3);

        $this->checkThankYouPageAccessWithOrderAvailable($response, $order);
    }

    /** @test */
    public function an_order_with_a_single_item_can_be_stored_as_guest()
    {
        $response = $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem($this->productA),
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

        $this->assertDatabaseHasProductOrderItem($this->productA, $order);

        $this->checkThankYouPageAccessWithOrderAvailable($response, $order);
    }

    /** @test */
    public function an_order_with_a_single_item_can_be_stored_as_user()
    {
        $this->be($this->testUser);

        $response = $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem($this->productB, 2),
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

        $this->assertDatabaseHasProductOrderItem($this->productB, $order, 2);

        $this->checkThankYouPageAccessWithOrderAvailable($response, $order);
    }

    /** @test */
    public function thank_you_page_throws_404_when_no_order_was_found(): void
    {
        $this->get(route('shop.order.thankYou', [
            'uuid' => 'a-b-c-d-e-f-not-found'
        ]))->assertStatus(404);
    }

    protected function checkThankYouPageAccessWithOrderAvailable(
        TestResponse $response,
        OrderContract $order
    ): void {
        $response->assertStatus(201)
            ->assertJson([
                'redirectUrl' => $order->getThankYouUrl()
            ]);

        $this->get($order->getThankYouUrl())
            ->assertStatus(200)
            ->assertViewIs('shop::thankYou');
    }
}
