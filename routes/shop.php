<?php

use DV5150\Shop\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('order/{uuid}/thank-you', [ShopController::class, 'showThankYouPage'])
    ->name('order.thankYou');
