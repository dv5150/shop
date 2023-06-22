<?php

namespace DV5150\Shop\Http\Requests;

use DV5150\Shop\Contracts\Transformers\OrderDataTransformerContract;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return app(OrderDataTransformerContract::class)->rules();
    }

    public function attributes(): array
    {
        return trans('shop::validation.attributes');
    }
}
