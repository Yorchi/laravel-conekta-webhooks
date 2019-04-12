<?php

namespace Yorchi\LaravelConektaWebhooks;

class ConektaWebhookCall
{
    public $payload = [];

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function type(): string
    {
        return camel_case(str_replace('.', '_', $this->payload['type']));
    }

    public function rawType(): string
    {
        return $this->payload['type'];
    }

    public function data(): array
    {
        return $this->payload['data'];
    }

    public function object(): array
    {
        return $this->payload['data']['object'];
    }
}
