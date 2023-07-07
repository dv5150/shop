<?php

namespace DV5150\Shop\Tests\Unit\Models;

use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Tests\Mock\Models\Category;
use DV5150\Shop\Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;

class ProductTest extends TestCase
{
    protected Category $categoryA;
    protected Category $categoryB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryA = Category::factory()->create();
        $this->categoryB = Category::factory()->create();
    }

    /** @test */
    public function a_product_has_multiple_categories()
    {
        $this->productA->categories()->sync([
            $this->categoryA->getKey(),
            $this->categoryB->getKey(),
        ]);

        $this->productB->categories()->sync([
            $this->categoryA->getKey(),
            $this->categoryB->getKey(),
        ]);

        $this->assertInstanceOf(Collection::class, $this->productA->categories);
        $this->assertInstanceOf(Collection::class, $this->productB->categories);

        $this->productA->categories->each(function ($category) {
            $this->assertInstanceOf(Category::class, $category);
        });

        $this->productB->categories->each(function ($category) {
            $this->assertInstanceOf(Category::class, $category);
        });

        $this->assertInstanceOf(Collection::class, $this->categoryA->products);
        $this->assertInstanceOf(Collection::class, $this->categoryB->products);

        $this->categoryA->products->each(function ($product) {
            $this->assertInstanceOf(SellableItemContract::class, $product);
        });

        $this->categoryB->products->each(function ($product) {
            $this->assertInstanceOf(SellableItemContract::class, $product);
        });
    }
}