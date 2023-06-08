<?php

namespace DV5150\Shop\Http\Requests;

use DV5150\Shop\Contracts\OrderDataTransformerContract;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return app(OrderDataTransformerContract::class)->rules();
    }
}
