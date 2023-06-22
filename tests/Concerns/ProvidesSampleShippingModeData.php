<?php

namespace DV5150\Shop\Tests\Concerns;

use DV5150\Shop\Contracts\Models\PaymentModeContract;
use DV5150\Shop\Contracts\Models\ShippingModeContract;
use Illuminate\Support\Collection;

trait ProvidesSampleShippingModeData
{
    protected ShippingModeContract $shippingMode;

    protected string $shippingModeProvider;
    protected string $paymentModeProvider;

    public function setUpSampleShippingModeData()
    {
        $this->shippingMode = config('shop.models.shippingMode')::factory()->create([
            'price_gross' => 490.0,
        ]);

        $this->shippingMode->paymentModes()
            ->sync(
                config('shop.models.paymentMode')::factory()->count(3)->create()
            );

        $this->shippingModeProvider = $this->shippingMode->getProvider();
        $this->paymentModeProvider = $this->shippingMode->paymentModes->first()->getProvider();
    }

    protected function expectedShippingModeCartItem(
        string $provider,
        string $name,
        float $priceGross,
        string $componentName,
        Collection $paymentModes,
    ): array {
        return [
            'provider' => $provider,
            'name' => $name,
            'priceGross' => $priceGross,
            'componentName' => $componentName,
            'paymentModes' => $paymentModes->map(function (PaymentModeContract $paymentMode) {
                return $this->expectedPaymentModeCartItem($paymentMode);
            })->all(),
        ];
    }

    protected function expectedPaymentModeCartItem(PaymentModeContract $paymentMode): array
    {
        return [
            'provider' => $paymentMode->getProvider(),
            'name' => $paymentMode->getName(),
            'priceGross' => $paymentMode->getPriceGross(),
        ];
    }

    public function expectShippingModeInCart(ShippingModeContract $shippingMode): array
    {
        return $this->expectedShippingModeCartItem(
            provider: $shippingMode->getProvider(),
            name: $shippingMode->getProvider(),
            priceGross: $shippingMode->getPriceGross(),
            componentName: $shippingMode->getComponentName(),
            paymentModes: $shippingMode->paymentModes,
        );
    }

    public function expectShippingModeOrderItem()
    {

    }
}
