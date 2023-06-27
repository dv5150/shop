<?php

namespace DV5150\Shop\Contracts\Services;

interface MessageServiceContract
{
    public function addPositiveMessage(string $key, string $message): void;
    public function addNeutralMessage(string $key, string $message): void;
    public function addNegativeMessage(string $key, string $message): void;
    public function all(): ?array;
}