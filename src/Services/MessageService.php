<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Contracts\Services\MessageServiceContract;
use Illuminate\Support\Arr;

class MessageService implements MessageServiceContract
{
    protected array $messages = [];

    public function addPositiveMessage(string $key, string $message): void
    {
        $this->addMessage('positive', $key, $message);
    }

    public function addNeutralMessage(string $key, string $message): void
    {
        $this->addMessage('neutral', $key, $message);
    }

    public function addNegativeMessage(string $key, string $message): void
    {
        $this->addMessage('negative', $key, $message);
    }

    public function all(): ?array
    {
        return empty($this->messages) ? null : $this->messages;
    }

    protected function addMessage(string $type, $key, $message): void
    {
        Arr::set($this->messages, $key, [
            'type' => $type,
            'text' => $message,
        ]);
    }
}