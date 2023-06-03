<?php

namespace DV5150\Shop\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface ProductContract
{
    public function discounts(): MorphMany;
    public function getName(): string;
    public function getPriceGross(): float;
    public function isDigitalProduct(): bool;
}
