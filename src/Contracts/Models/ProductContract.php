<?php

namespace DV5150\Shop\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface ProductContract
{
    public function discounts(): MorphToMany;
    public function getName(): string;
    public function getPriceGross(): float;
    public function isDigitalProduct(): bool;
}
