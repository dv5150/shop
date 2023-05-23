<?php

namespace DV5150\Shop\Concerns;

trait ProvidesValueDealData
{
    public function getFullName(): ?string
    {
        return "{$this->getValue()} {$this->getUnit()} OFF ({$this->getName()})";
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
        return ':currency';
    }
}
