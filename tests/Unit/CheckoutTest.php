<?php

namespace DV5150\Shop\Tests\Unit;

use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Tests\Concerns\ProvidesSampleOrderData;
use DV5150\Shop\Tests\TestCase;
use DV5150\Shop\Tests\Mock\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;

class CheckoutTest extends TestCase
{
    use ProvidesSampleOrderData;

    protected ProductContract $productA;
    protected ProductContract $productB;

    protected User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpSampleOrderData();

        $this->productA = config('shop.models.product')::factory()
            ->create()
            ->refresh();

        $this->productB = config('shop.models.product')::factory()
            ->create()
            ->refresh();

        $this->testUser = config('shop.models.user')::create([
            'name' => 'Johnny Jackson',
            'email' => 'johnny+12345@jackson.com',
            'password' => Hash::make('testing'),
        ]);

        $this->expectedProductAData = [
            'product_id' => $this->productA->getID(),
            'name' => $this->productA->getName(),
            'price_gross' => $this->productA->getPriceGross(),
        ];

        $this->expectedProductBData = [
            'product_id' => $this->productB->getID(),
            'name' => $this->productB->getName(),
            'price_gross' => $this->productB->getPriceGross(),
        ];
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
                            'id' => $this->productA->getID(),
                        ],
                        'quantity' => 2,
                    ],
                    [
                        'item' => [
                            'id' => $this->productB->getID(),
                        ],
                        'quantity' => 4,
                    ],
                ],
            ])
        );

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => null,
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedProductAData, [
            'order_id' => config('shop.models.order')::first()->getKey(),
            'quantity' => 2,
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedProductBData, [
            'order_id' => config('shop.models.order')::first()->getKey(),
            'quantity' => 4,
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
                            'id' => $this->productA->getID(),
                        ],
                        'quantity' => 5,
                    ],
                    [
                        'item' => [
                            'id' => $this->productB->getID(),
                        ],
                        'quantity' => 3,
                    ],
                ],
            ])
        );

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => $this->testUser->getKey(),
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedProductAData, [
            'order_id' => config('shop.models.order')::first()->getKey(),
            'quantity' => 5,
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedProductBData, [
            'order_id' => config('shop.models.order')::first()->getKey(),
            'quantity' => 3,
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
                            'id' => $this->productA->getID(),
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
                            'id' => $this->productB->getID(),
                        ],
                        'quantity' => 2,
                    ],
                ],
            ])
        );

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => $this->testUser->getKey(),
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedProductBData, [
            'order_id' => config('shop.models.order')::first()->getKey(),
            'quantity' => 2,
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
