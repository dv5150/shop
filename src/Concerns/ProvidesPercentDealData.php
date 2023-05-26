<?php

namespace DV5150\Shop\Concerns;

trait ProvidesPercentDealData
{
    public function getFullName(): ?string
    {
        return "{$this->getTypeName()}: {$this->getValue()}{$this->getUnit()}";
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getUnit(): string
    {
        return '%';
    }
}
