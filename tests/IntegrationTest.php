<?php

namespace Yorchi\LaravelConektaWebhooks\Tests;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Yorchi\LaravelConektaWebhooks\ConektaWebhookCall;

class IntegrationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Event::fake();

        Bus::fake();

        Route::conektaWebhooks('conekta-webhooks');

        config(['conekta-webhooks.jobs' => ['chargeCreated' => DummyJob::class]]);
    }

    /** @test */
    public function it_should_convert_event_to_method()
    {
        $method = (new ConektaWebhookCall($this->getTestPayload()))->type();
        $this->assertEquals('chargeCreated', $method);
    }

    /** @test */
    public function it_can_handle_a_valid_request()
    {
        $this->disableExceptionHandling();

        $payload = $this->getTestPayload();

        $this
            ->postJson('conekta-webhooks', $payload)
            ->assertSuccessful();

        Event::assertDispatched('conekta-webhooks::chargeCreated', function ($event, $eventPayload) {
            if (!$eventPayload instanceof ConektaWebhookCall) {
                return false;
            }

            if ('chargeCreated' != $eventPayload->type()) {
                return false;
            }

            return true;
        });

        Bus::assertDispatched(DummyJob::class, function (DummyJob $job) {
            return 'chargeCreated' === $job->conektaWebhookCall->type();
        });
    }

    /**
     * @return array
     */
    public function getTestPayload(): array
    {
        return [
            'data' => [
                'object' => [
                    'id' => '234452352345234',
                    'livemode' => false,
                    'status' => 'paid',
                    'currency' => 'MXN',
                    'amount' => 20000,
                    'fee' => 963,
                    'customer_id' => 'cus_23434',
                    'object' => 'charge',
                    'payment_method' => [
                        'name' => 'Jorge Lopez',
                        'exp_month' => '12',
                        'exp_year' => '19',
                        'auth_code' => null,
                        'object' => 'card_payment',
                        'last4' => '4242',
                        'brand' => 'visa',
                    ],
                    'order_id' => 'ord_qe4355345',
                ],
            ],
            'created_at' => 1379796210,
            'type' => 'charge.created',
        ];

        return $payload;
    }
}
