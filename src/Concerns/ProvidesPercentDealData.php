<?php

namespace DV5150\Shop\Concerns;

trait ProvidesPercentDealData
{
    public function getFullName(): ?string
    {
        return "{$this->getValue()}{$this->getUnit()} ({$this->getName()})";
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
