<?php

namespace DV5150\Shop\Tests\Unit\Models;

use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Tests\Concerns\CreatesDiscountsForProducts;
use DV5150\Shop\Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use TypeError;

class DiscountTest extends TestCase
{
    use CreatesDiscountsForProducts;

    /** @test */
    public function base_discount_only_retrieves_product_contract_as_discountable()
    {
        $discount = $this->createPercentDiscountForProduct(
            $this->productA,
            '10% OFF discount',
            10.0
        );

        $this->assertInstanceOf(ProductContract::class, $discount->getDiscountable());

        $user = config('shop.models.user')::create([
            'email' => 'abcdef@fedcba+safemail.com',
            'name' => 'Test User',
            'password' => Hash::make('password'),
        ]);

        $discount->discountable()->associate($user);

        $this->expectException(TypeError::class);
        $this->assertInstanceOf(ProductContract::class, $discount->getDiscountable());
    }
}