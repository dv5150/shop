<?php

namespace DV5150\Shop\Tests\Concerns;

use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;
use DV5150\Shop\Contracts\Models\CartItemCapsuleContract;
use DV5150\Shop\Contracts\Models\OrderContract;
use DV5150\Shop\Contracts\Models\ProductContract;
use DV5150\Shop\Models\Deals\Coupon;
use DV5150\Shop\Models\Deals\Discount;
use Illuminate\Database\Eloquent\Model;

trait ProvidesSampleProductData
{
    protected ProductContract|Model $productA;
    protected ProductContract|Model $productB;
    protected ProductContract|Model $productC;
    protected ProductContract|Model $productD;
    protected ProductContract|Model $productE;

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
        ProductContract $product,
        int $quantity = 1,
        Discount $discount = null,
        float $overwriteGrossPrice = null,
    ): array {
        /** @var CartItemCapsuleContract $capsule */
        $capsule = new (config('shop.support.cartItemCapsule'))(
            product: $product,
            quantity: $quantity
        );

        return $this->expectedProductCartitem(
            id: $capsule->getProduct()->getKey(),
            name: $capsule->getProduct()->getName(),
            priceGross: $overwriteGrossPrice ?? $capsule->getPriceGross(),
            priceGrossOriginal: $capsule->getOriginalProductPriceGross(),
            quantity: $capsule->getQuantity(),
            subtotal: $quantity * ($overwriteGrossPrice ?? $capsule->getPriceGross()),
            discount: $discount?->getDiscount(),
        );
    }

    public function makeProductCartDataItem(ProductContract $product, int $quantity = 1): array
    {
        return $this->getProductCartDataItem(
            id: $product->getKey(),
            quantity: $quantity,
        );
    }

    public function assertDatabaseHasProductOrderItem(
        ProductContract $product,
        OrderContract $order,
        int $quantity = 1,
        string $info = null,
        float $overwriteGrossPrice = null,
    ): void {
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->getKey(),
            'product_id' => $product->getKey(),
            'name' => $product->getName(),
            'quantity' => $quantity,
            'price_gross' => $overwriteGrossPrice ?? $product->getPriceGross(),
            'info' => $info,
        ]);
    }

    public function assertDatabaseHasCouponOrderItem(
        Coupon $coupon,
        OrderContract $order,
        float $priceGross,
        string $info = null,
    ): void {
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->getKey(),
            'product_id' => null,
            'name' => $coupon->getName(),
            'quantity' => 1,
            'price_gross' => $priceGross,
            'info' => $info,
        ]);
    }

    protected function expectedProductCartitem(
        string               $id,
        string               $name,
        string               $priceGross,
        string               $priceGrossOriginal,
        int                  $quantity,
        float                $subtotal,
        BaseDiscountContract $discount = null,
    ): array {
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

    protected function expectedProductCartItemDiscount(BaseDiscountContract $discount): array {
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
