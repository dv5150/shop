<?php

namespace DV5150\Shop\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface SellableItemContract
{
    public static function bootDetachesAllDiscounts(): void;
    public static function bootDetachesFromOrderItems(): void;

    public function discounts(): MorphToMany;
    public function getName(): string;
    public function getDescription(): ?string;
    public function getPriceGross(): float;
    public function isDigitalItem(): bool;
}
