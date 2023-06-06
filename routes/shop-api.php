<?php

use DV5150\Shop\Controllers\API\CartAPIController;
use DV5150\Shop\Controllers\API\CheckoutAPIController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'cart', 'as' => 'cart.'], function () {
    Route::group(['prefix' => 'coupon', 'as' => 'coupon.'], function () {
        Route::post('{code}', [CartAPIController::class, 'setCoupon'])
            ->name('store');
        Route::delete('/', [CartAPIController::class, 'removeCoupon'])
            ->name('erase');
    });

    Route::group(['prefix' => 'shipping-mode', 'as' => 'shippingMode.'], function () {
        Route::post('{provider}', [CartAPIController::class, 'setShippingMode'])
            ->name('store');
    });

    Route::group(['prefix' => 'payment-mode', 'as' => 'paymentMode.'], function () {
        Route::post('{provider}', [CartAPIController::class, 'setPaymentMode'])
            ->name('store');
    });

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
    Route::post('/', [CheckoutAPIController::class, 'store'])
        ->name('store');
});
