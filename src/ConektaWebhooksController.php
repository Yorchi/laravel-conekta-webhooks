<?php

namespace Yorchi\LaravelConektaWebhooks;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yorchi\LaravelConektaWebhooks\ConektaWebhookCall;
use Yorchi\LaravelConektaWebhooks\Exceptions\WebhookFailed;

class ConektaWebhooksController extends Controller
{
    public function __invoke(Request $request)
    {
        $eventPayload = json_decode($request->getContent(), true);

        if (!isset($eventPayload['type'])) {
            throw WebhookFailed::missingType($request);
        }

        $conektaWebhookCall = new ConektaWebhookCall($eventPayload);

        $type = $conektaWebhookCall->type();

        event("conekta-webhooks::{$type}", $conektaWebhookCall);

        $jobClass = $this->determineJobClass($type);

        if ('' === $jobClass) {
            return;
        }

        if (!class_exists($jobClass)) {
            throw WebhookFailed::jobClassDoesNotExist($jobClass, $conektaWebhookCall);
        }

        dispatch(new $jobClass($conektaWebhookCall));

        return response('Webhook received!');
    }

    protected function determineJobClass(string $type): string
    {
        return config("conekta-webhooks.jobs.{$type}", '');
    }
}
