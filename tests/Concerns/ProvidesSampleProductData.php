<?php

namespace DV5150\Shop\Tests\Concerns;

use DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract;
use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;
use DV5150\Shop\Contracts\Models\OrderContract;
use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Contracts\Support\ShopItemCapsuleContract;
use Illuminate\Support\Str;

trait ProvidesSampleProductData
{
    protected SellableItemContract $productA;
    protected SellableItemContract $productB;
    protected SellableItemContract $productC;
    protected SellableItemContract $productD;
    protected SellableItemContract $productE;

    public function setUpSampleProductData()
    {
        $this->productA = config('shop.models.product')::factory()->create([
            'price_gross' => 500.0,
        ]);

        $this->productB = config('shop.models.product')::factory()->create([
            'price_gross' => 1500.0,
        ]);

        $this->productC = config('shop.models.product')::factory()->create([
            'price_gross' => 1800.0,
        ]);

        $this->productD = config('shop.models.product')::factory()->create([
            'price_gross' => 4200.0,
        ]);

        $this->productE = config('shop.models.product')::factory()->create([
            'price_gross' => 17300.0,
        ]);
    }

    public function expectProductInCart(
        SellableItemContract $sellableItem,
        int $quantity = 1,
        BaseDiscountContract $discount = null,
        float $overwriteGrossPrice = null,
    ): array
    {
        /** @var ShopItemCapsuleContract $capsule */
        $capsule = new (config('shop.support.shopItemCapsule'))(
            sellableItem: $sellableItem,
            quantity: $quantity
        );

        return $this->expectedProductCartitem(
            id: $capsule->getSellableItem()->getKey(),
            name: $capsule->getSellableItem()->getName(),
            priceGross: $overwriteGrossPrice ?? $capsule->getPriceGross(),
            priceGrossOriginal: $capsule->getOriginalPriceGross(),
            quantity: $capsule->getQuantity(),
            subtotal: $quantity * ($overwriteGrossPrice ?? $capsule->getPriceGross()),
            discount: $discount?->getDiscount(),
        );
    }

    public function makeProductCartDataItem(SellableItemContract $sellableItem, int $quantity = 1): array
    {
        return $this->getProductCartDataItem(
            id: $sellableItem->getKey(),
            quantity: $quantity,
        );
    }

    public function assertDatabaseHasProductOrderItem(
        SellableItemContract $sellableItem,
        OrderContract $order,
        int $quantity = 1,
        string $info = null,
        float $overwriteGrossPrice = null,
    ): void
    {
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->getKey(),
            'sellable_type' => $sellableItem::class,
            'sellable_id' => $sellableItem->getKey(),
            'name' => $sellableItem->getName(),
            'quantity' => $quantity,
            'price_gross' => $overwriteGrossPrice ?? $sellableItem->getPriceGross(),
            'info' => $info,
            'type' => 'product',
        ]);
    }

    public function assertDatabaseHasCouponOrderItem(
        BaseCouponContract $coupon,
        OrderContract $order,
        float $priceGross,
        string $info = null,
    ): void
    {
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->getKey(),
            'sellable_type' => $coupon::class,
            'sellable_id' => $coupon->getKey(),
            'name' => $coupon->getName(),
            'quantity' => 1,
            'price_gross' => $priceGross,
            'info' => $info,
            'type' => Str::kebab(class_basename($coupon->getCoupon())),
        ]);
    }

    protected function expectedProductCartitem(
        string $id,
        string $name,
        string $priceGross,
        string $priceGrossOriginal,
        int $quantity,
        float $subtotal,
        BaseDiscountContract $discount = null,
    ): array
    {
        return [
            'item' => [
                'id' => $id,
                'name' => $name,
                'price_gross' => $priceGross,
                'price_gross_original' => $priceGrossOriginal,
                'discount' => $discount
                    ? $this->expectedProductCartItemDiscount($discount)
                    : null
            ],
            'quantity' => $quantity,
            'subtotal' => $subtotal,
        ];
    }

    protected function expectedProductCartItemDiscount(BaseDiscountContract $discount): array
    {
        return [
            'name' => $discount->getName(),
            'unit' => $discount->getUnit(),
            'value' => $discount->getValue(),
        ];
    }

    protected function getProductCartDataItem(string $id, int $quantity): array
    {
        return [
            'item' => [
                'id' => $id,
            ],
            'quantity' => $quantity,
        ];
    }
}
