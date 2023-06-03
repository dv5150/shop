<?php

namespace DV5150\Shop\Tests\Unit;

use DV5150\Shop\Tests\Concerns\ProvidesSampleOrderData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleProductData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleShippingModeData;
use DV5150\Shop\Tests\Mock\Models\User;
use DV5150\Shop\Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;

class CheckoutTest extends TestCase
{
    use ProvidesSampleOrderData,
        ProvidesSampleProductData,
        ProvidesSampleShippingModeData;

    protected User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpSampleOrderData();
        $this->setUpSampleProductData();
        $this->setUpSampleShippingModeData();

        $this->testUser = config('shop.models.user')::create([
            'name' => 'Johnny Jackson',
            'email' => 'johnny+12345@jackson.com',
            'password' => Hash::make('testing'),
        ]);
    }

    /** @test */
    public function an_order_with_multiple_items_can_be_stored_as_guest()
    {
        $response = $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    [
                        'item' => [
                            'id' => $this->productA->getKey(),
                        ],
                        'quantity' => 2,
                    ],
                    [
                        'item' => [
                            'id' => $this->productB->getKey(),
                        ],
                        'quantity' => 4,
                    ],
                ],
            ])
        );

        $orderKey = config('shop.models.order')::first()->getKey();

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => null,
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedProductAData, [
            'order_id' => $orderKey,
            'quantity' => 2,
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedProductBData, [
            'order_id' => $orderKey,
            'quantity' => 4,
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedShippingModeOrderItemData, [
            'order_id' => $orderKey
        ]));

        $this->checkThankYouPageAccessWithOrderAvailable($response);
    }

    /** @test */
    public function an_order_with_multiple_items_can_be_stored_as_user()
    {
        $this->be($this->testUser);

        $response = $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    [
                        'item' => [
                            'id' => $this->productA->getKey(),
                        ],
                        'quantity' => 5,
                    ],
                    [
                        'item' => [
                            'id' => $this->productB->getKey(),
                        ],
                        'quantity' => 3,
                    ],
                ],
            ])
        );

        $orderKey = config('shop.models.order')::first()->getKey();

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => $this->testUser->getKey(),
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedProductAData, [
            'order_id' => $orderKey,
            'quantity' => 5,
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedProductBData, [
            'order_id' => $orderKey,
            'quantity' => 3,
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedShippingModeOrderItemData, [
            'order_id' => $orderKey
        ]));

        $this->checkThankYouPageAccessWithOrderAvailable($response);
    }

    /** @test */
    public function an_order_with_a_single_item_can_be_stored_as_guest()
    {
        $response = $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    [
                        'item' => [
                            'id' => $this->productA->getKey(),
                        ],
                        'quantity' => 1,
                    ],
                ],
            ])
        );

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => null,
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedProductAData, [
            'order_id' => config('shop.models.order')::first()->getKey(),
            'quantity' => 1,
        ]));

        $this->checkThankYouPageAccessWithOrderAvailable($response);
    }

    /** @test */
    public function an_order_with_a_single_item_can_be_stored_as_user()
    {
        $this->be($this->testUser);

        $response = $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    [
                        'item' => [
                            'id' => $this->productB->getKey(),
                        ],
                        'quantity' => 2,
                    ],
                ],
            ])
        );

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => $this->testUser->getKey(),
        ]));

        $orderKey = config('shop.models.order')::first()->getKey();

        $this->assertDatabaseHas('order_items', array_merge($this->expectedProductBData, [
            'order_id' => $orderKey,
            'quantity' => 2,
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedShippingModeOrderItemData, [
            'order_id' => $orderKey
        ]));

        $this->checkThankYouPageAccessWithOrderAvailable($response);
    }

    /** @test */
    public function thank_you_page_throws_404_when_no_order_was_found(): void
    {
        $this->get(route('shop.order.thankYou', [
            'uuid' => 'a-b-c-d-e-f-not-found'
        ]))->assertStatus(404);
    }

    public function checkThankYouPageAccessWithOrderAvailable(TestResponse $response): void
    {
        $order = config('shop.models.order')::first();

        $response->assertStatus(201)
            ->assertJson([
                'redirectUrl' => $order->getThankYouUrl()
            ]);

        $this->get($order->getThankYouUrl())
            ->assertStatus(200)
            ->assertViewIs('shop::thankYou');
    }
}
