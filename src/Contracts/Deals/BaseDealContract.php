<?php

namespace DV5150\Shop\Contracts\Deals;

use Illuminate\Contracts\Support\Arrayable;

interface BaseDealContract extends Arrayable
{
    public function getShortName(): ?string;
    public function getName(): ?string;
    public function getValue(): float;
    public function getUnit(): string;
}
