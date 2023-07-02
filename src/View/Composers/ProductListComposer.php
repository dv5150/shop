<?php

namespace DV5150\Shop\View\Composers;

use DV5150\Shop\Contracts\Services\ProductListComposerServiceContract;
use Illuminate\View\View;

class ProductListComposer
{
    public function compose(View $view)
    {
        $view->with(app(ProductListComposerServiceContract::class)->getProductListData());
    }
}