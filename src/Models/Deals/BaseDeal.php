<?php

namespace DV5150\Shop\Models\Deals;

use Illuminate\Database\Eloquent\Model;

abstract class BaseDeal extends Model
{
    abstract public function getName(): ?string;
    abstract public function getValue(): float;
    abstract public function getUnit(): string;
}