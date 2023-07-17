<?php

namespace DV5150\Shop\Models\Default;

use DV5150\Shop\Contracts\Models\OrderContract;
use DV5150\Shop\Contracts\Models\OrderItemContract;
use DV5150\Shop\Contracts\Models\PaymentModeContract;
use DV5150\Shop\Contracts\Models\ShippingModeContract;
use DV5150\Shop\Contracts\Models\ShopUserContract;
use DV5150\Shop\Facades\Shop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model implements OrderContract
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('shop.models.user'));
    }

    public function items(): HasMany
    {
        return $this->hasMany(config('shop.models.orderItem'));
    }

    public function shippingMode(): BelongsTo
    {
        return $this->belongsTo(config('shop.models.shippingMode'));
    }

    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(config('shop.models.paymentMode'));
    }

    public function payment(): HasOne
    {
        return $this->hasOne(config('shop.models.payment'));
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->has('payment');
    }

    public function scopeUnPaid(Builder $query): Builder
    {
        return $query->doesntHave('payment');
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getUser(): ShopUserContract
    {
        return $this->user;
    }

    public function getShipingMode(): ShippingModeContract
    {
        return $this->shippingMode;
    }

    public function getPaymentMode(): PaymentModeContract
    {
        return $this->paymentMode;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getShippingName(): string
    {
        return $this->shipping_name;
    }

    public function getShippingZipCode(): string
    {
        return $this->shipping_zip_code;
    }

    public function getShippingCity(): string
    {
        return $this->shipping_city;
    }

    public function getShippingAddress(): string
    {
        return $this->shipping_address;
    }

    public function getShippingComment(): string
    {
        return $this->shipping_comment;
    }

    public function getBillingName(): string
    {
        return $this->billing_name;
    }

    public function getBillingZipCode(): string
    {
        return $this->billing_zip_code;
    }

    public function getBillingCity(): string
    {
        return $this->billing_city;
    }

    public function getBillingAddress(): string
    {
        return $this->billing_address;
    }

    public function getBillingTaxNumber(): string
    {
        return $this->billing_tax_number;
    }

    public function getTotalGrossPrice(): float
    {
        return $this->items->map(fn (OrderItemContract $orderItem) => $orderItem->getSubtotal())->sum();
    }

    public function getThankYouUrl(): string
    {
        if (Shop::isFrontendInstalled()) {
            return route('shop.order.thankYou', [
                'order' => $this->uuid
            ]);
        }

        return route('home');
    }

    public function getOnlinePaymentUrl(): string
    {
        return route('shop.pay', [
            'paymentProvider' => $this->getPaymentMode()->getProvider(),
            'order' => $this->getUuid(),
        ]);
    }

    public function requiresOnlinePayment(): bool
    {
        return $this->isUnPaid()
            && $this->hasOnlinePaymentModeAttached();
    }

    public function isPaid(): bool
    {
        return $this->payment()
            ->exists();
    }

    public function isUnPaid(): bool
    {
        return ! $this->isPaid();
    }

    public function hasOnlinePaymentModeAttached(): bool
    {
        return $this->paymentMode()
            ->where('is_online_payment', true)
            ->exists();
    }
}
