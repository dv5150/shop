<?php

namespace DV5150\Shop\Contracts;

interface ProductContract
{
    public function getID();
    public function getName(): string;
    public function getPriceGross(): float;
    public function isDigitalProduct(): bool;
}
