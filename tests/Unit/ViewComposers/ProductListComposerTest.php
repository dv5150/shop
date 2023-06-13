<?php

namespace DV5150\Shop\Tests\Unit\ViewComposers;

use DV5150\Shop\Tests\TestCase;
use Illuminate\Support\Facades\Blade;

class ProductListComposerTest extends TestCase
{
    /** @test */
    public function product_list_view_partial_receives_the_product_list_json_data()
    {
        $blade = Blade::render('shop::partials.productList');

        $json = config('shop.resources.productList')::collection(
            config('shop.models.product')::with([
                'categories', 'discounts'
            ])->get()
        )->toJson();

        $this->assertSame('<product-list :products="'.$json.'"></product-list>', html_entity_decode($blade));
    }
}