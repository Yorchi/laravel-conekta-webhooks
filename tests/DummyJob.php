<?php

namespace Yorchi\LaravelConektaWebhooks\Tests;

use Yorchi\LaravelConektaWebhooks\ConektaWebhookCall;

class DummyJob
{
    /** @var \Yorchi\LaravelConektaWebhooks\ConektaWebhookCall */
    public $conektaWebhookCall;

    public function __construct(ConektaWebhookCall $conektaWebhookCall)
    {
        $this->conektaWebhookCall = $conektaWebhookCall;
    }

    public function handle()
    {
    }
}
