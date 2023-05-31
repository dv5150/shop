<?php

namespace DV5150\Shop\Contracts;

interface ShippingModeContract
{
    public function getID();
    public function getName(): string;
    public function getShortName(): string;
    public function getProvider(): string;
    public function getPriceGross(): float;
    public function toOrderItem(): OrderItemContract;
}
