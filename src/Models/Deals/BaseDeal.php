<?php

namespace DV5150\Shop\Models\Deals;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class BaseDeal extends Model
{
    use HasFactory;

    abstract public function getName(): ?string;
    abstract public function getValue(): float;
    abstract public function getUnit(): string;
}