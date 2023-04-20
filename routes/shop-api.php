<?php

use DV5150\Shop\Controllers\API\CartAPIController;
use DV5150\Shop\Controllers\API\CheckoutAPIController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'cart', 'as' => 'cart.'], function () {
    Route::get('/', [CartAPIController::class, 'index'])
        ->name('index');
    Route::post('{product}/add/{quantity?}', [CartAPIController::class, 'store'])
        ->name('store');
    Route::post('{product}/remove/{quantity?}', [CartAPIController::class, 'remove'])
        ->name('remove');
    Route::delete('{product}', [CartAPIController::class, 'erase'])
        ->name('erase');
});

Route::group(['prefix' => 'checkout', 'as' => 'checkout.'], function () {
    Route::get('shipping-modes', [CheckoutAPIController::class, 'shippingModes'])
        ->name('shippingMode.index');

    Route::get('payment-modes', [CheckoutAPIController::class, 'paymentModes'])
        ->name('paymentMode.index');

    Route::post('/', [CheckoutAPIController::class, 'store'])
        ->name('store');
});
