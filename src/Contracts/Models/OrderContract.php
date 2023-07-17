<?php

namespace DV5150\Shop\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

interface OrderContract
{
    public function user(): BelongsTo;
    public function items(): HasMany;
    public function shippingMode(): BelongsTo;
    public function paymentMode(): BelongsTo;
    public function payment(): HasOne;

    public function getUuid(): string;
    public function getUser(): ShopUserContract;
    public function getShipingMode(): ShippingModeContract;
    public function getPaymentMode(): PaymentModeContract;

    public function getEmail(): string;
    public function getPhone(): string;
    public function getComment(): string;

    public function getShippingName(): string;
    public function getShippingZipCode(): string;
    public function getShippingCity(): string;
    public function getShippingAddress(): string;
    public function getShippingComment(): string;

    public function getBillingName(): string;
    public function getBillingZipCode(): string;
    public function getBillingCity(): string;
    public function getBillingAddress(): string;
    public function getBillingTaxNumber(): string;

    public function getTotalGrossPrice(): float;

    public function getThankYouUrl(): string;
    public function getOnlinePaymentUrl(): string;

    public function requiresOnlinePayment(): bool;
}
