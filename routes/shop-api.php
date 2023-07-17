<?php

use DV5150\Shop\Contracts\Controllers\API\CartAPIControllerContract;
use DV5150\Shop\Contracts\Controllers\API\CheckoutAPIControllerContract;
use DV5150\Shop\Contracts\Controllers\PaymentControllerContract;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'cart', 'as' => 'cart.'], function () {
    Route::group(['prefix' => 'coupon', 'as' => 'coupon.'], function () {
        Route::post('{code}', [app(CartAPIControllerContract::class), 'setCoupon'])
            ->name('store');
        Route::delete('/', [app(CartAPIControllerContract::class), 'removeCoupon'])
            ->name('erase');
    });

    Route::group(['prefix' => 'shipping-mode', 'as' => 'shippingMode.'], function () {
        Route::post('{provider}', [app(CartAPIControllerContract::class), 'setShippingMode'])
            ->name('store');
    });

    Route::group(['prefix' => 'payment-mode', 'as' => 'paymentMode.'], function () {
        Route::post('{provider}', [app(CartAPIControllerContract::class), 'setPaymentMode'])
            ->name('store');
    });

    Route::get('/', [app(CartAPIControllerContract::class), 'index'])
        ->name('index');
    Route::post('{product}/add/{quantity?}', [app(CartAPIControllerContract::class), 'store'])
        ->name('store');
    Route::post('{product}/remove/{quantity?}', [app(CartAPIControllerContract::class), 'remove'])
        ->name('remove');
    Route::delete('{product}', [app(CartAPIControllerContract::class), 'erase'])
        ->name('erase');
});

Route::group(['prefix' => 'checkout', 'as' => 'checkout.'], function () {
    Route::post('/', [app(CheckoutAPIControllerContract::class), 'store'])
        ->name('store');
});

Route::group(['prefix' => 'payment', 'as' => 'payment.'], function () {
    Route::post('{paymentProvider}/webhook', [app(PaymentControllerContract::class), 'webhook'])
        ->name('webhook')
        ->withoutMiddleware([VerifyCsrfToken::class]);
});
