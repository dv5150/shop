<?php

namespace DV5150\Shop\Contracts\Deals\Discounts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface DiscountContract extends BaseDiscountContract
{
    public function baseDiscount(): MorphOne;
    public function getBaseDiscount(): BaseDiscountContract;
}
