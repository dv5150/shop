<?php

namespace DV5150\Shop\Tests\Concerns;

use DV5150\Shop\Contracts\ProductContract;
use Illuminate\Database\Eloquent\Model;

trait ProvidesSampleProductData
{
    protected ProductContract|Model $productA;
    protected ProductContract|Model $productB;
    protected ProductContract|Model $productC;

    public array $expectedProductAData;
    public array $expectedProductBData;
    public array $expectedProductCData;

    public function setUpSampleProductData()
    {
        $this->productA = config('shop.models.product')::factory()
            ->create()
            ->refresh();

        $this->productB = config('shop.models.product')::factory()
            ->create()
            ->refresh();

        $this->productC = config('shop.models.product')::factory()
            ->create()
            ->refresh();

        $this->expectedProductAData = [
            'product_id' => $this->productA->getID(),
            'name' => $this->productA->getName(),
            'price_gross' => $this->productA->getPriceGross(),
        ];

        $this->expectedProductBData = [
            'product_id' => $this->productB->getID(),
            'name' => $this->productB->getName(),
            'price_gross' => $this->productB->getPriceGross(),
        ];

        $this->expectedProductCData = [
            'product_id' => $this->productC->getID(),
            'name' => $this->productC->getName(),
            'price_gross' => $this->productC->getPriceGross(),
        ];
    }
}
