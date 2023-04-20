<?php

namespace DV5150\Shop\Requests;

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
        return [
            'personalData.email' => 'required|email|max:255',
            'personalData.phone' => 'required|string|max:255',

            'shippingData.name' => 'required|string|max:255',
            'shippingData.zipCode' => 'required|string|max:255',
            'shippingData.city' => 'required|string|max:255',
            'shippingData.street' => 'required|string|max:255',
            'shippingData.comment' => 'required|string|max:255',

            'billingData.name' => 'required|string|max:255',
            'billingData.zipCode' => 'required|string|max:255',
            'billingData.city' => 'required|string|max:255',
            'billingData.street' => 'required|string|max:255',
            'billingData.taxNumber' => 'required|string|max:255',

            'cartData' => 'required|array|min:1',
            'cartData.*.item.id' => 'required|exists:products,id',
            'cartData.*.quantity' => 'required|integer|min:1',
        ];
    }
}
