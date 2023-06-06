<?php

namespace DV5150\Shop\View\Composers;

use Illuminate\View\View;

class ProductListComposer
{
    public function compose(View $view)
    {
        $view->with([
            'products' => config('shop.resources.productList')::collection(
                config('shop.models.product')::with([
                    'categories', 'discounts'
                ])->get()
            )->toJson()
        ]);
    }
}