<?php

use DV5150\Shop\Contracts\Controllers\PaymentControllerContract;
use Illuminate\Support\Facades\Route;

Route::get('payment/{paymentProvider}/pay/{order:uuid}', [app(PaymentControllerContract::class), 'pay'])
    ->name('pay');