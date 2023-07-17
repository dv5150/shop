<?php

namespace DV5150\Shop\Tests\Unit\Models;

use DV5150\Shop\Contracts\Models\OrderContract;
use DV5150\Shop\Contracts\Models\OrderItemContract;
use DV5150\Shop\Contracts\Models\PaymentModeContract;
use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Contracts\Models\ShippingModeContract;
use DV5150\Shop\Contracts\Models\ShopUserContract;
use DV5150\Shop\Tests\Concerns\ProvidesSampleOrderData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleShippingModeData;
use DV5150\Shop\Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderTest extends TestCase
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
    public function an_order_has_multiple_order_items()
    {
        $this->post(
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

        $order = config('shop.models.order')::with('items')->first();

        $this->assertInstanceOf(Collection::class, $order->items);

        $order->items->each(function ($orderItem) {
            $this->assertInstanceOf(OrderItemContract::class, $orderItem);
        });

        $order->items->each(function (OrderItemContract $orderItem) {
            $this->assertInstanceOf(OrderContract::class, $orderItem->getOrder());
        });

        $order->items->each(function (OrderItemContract $orderItem) {
            $this->assertInstanceOf(SellableItemContract::class, $orderItem->getSellable());
        });
    }
}