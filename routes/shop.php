<?php

use DV5150\Shop\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('order/{order}/thank-you', [ShopController::class, 'showThankYouPage'])
    ->name('order.thankYou');
