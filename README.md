# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/yorchi/laravel-conekta-webhooks.svg?style=flat-square)](https://packagist.org/packages/yorchi/laravel-conekta-webhooks)
[![Build Status](https://img.shields.io/travis/yorchi/laravel-conekta-webhooks/master.svg?style=flat-square)](https://travis-ci.org/yorchi/laravel-conekta-webhooks)
[![Quality Score](https://img.shields.io/scrutinizer/g/yorchi/laravel-conekta-webhooks.svg?style=flat-square)](https://scrutinizer-ci.com/g/yorchi/laravel-conekta-webhooks)
[![Total Downloads](https://img.shields.io/packagist/dt/yorchi/laravel-conekta-webhooks.svg?style=flat-square)](https://packagist.org/packages/yorchi/laravel-conekta-webhooks)

*Conekta* can notify your application of events using webhooks. This package can help you handle those webhooks. You can easily define jobs or events that should be dispatched when specific events hit your app.

## Installation

You can install the package via composer:

```bash
composer require yorchi/laravel-conekta-webhooks
```

The service provider will automatically register itself.

You must publish the config file with:

``` bash
$ php artisan vendor:publish --provider="\Yorchi\LaravelConektaWebhooks\LaravelConektaWebhooksServiceProvider" --tag="config"
```

This is the content of the config file that will be published ar `config/conekta-webhooks.php`:

``` php
return [
    /*
     * Here you can define the job that should be run when a certain webhook hits your
     * application.
     *
     * You can find a list of Conekta webhook types here:
     * https://developers.conekta.com/api#events
     */
    'jobs' => [
        // 'chargeCreated' => \App\Jobs\LaravelWebhooks\HandleCreatedCharge::class,
        // 'chargePaid' => \App\Jobs\LaravelWebhooks\HandlePaidCharge::class,
        // ...
    ],
];
```

Finally, take care of the routing: At the Conekta notification settings you must configure at what url Conekta webhooks should hit your app. In the routes file of your app you must pass that route to `Route::conektaWebhooks`:

``` php
Route::conektaWebhooks('webhook-route-configured-at-the-conekta-dashboard');
```

Behind the scenes this will register a *POST* route to a controller provided by this package. Because Conekta has no way of getting a csrf-token, you must add that route to the *except* array of the *VerifyCsrfToken* middleware:

``` php
protected $except = [
    'webhook-route-configured-at-the-conekta-dashboard',
];
```

## Usage

Conekta will send out webhooks for several event types. You can find the full list of events types in the Conekta documentation.

Unless something wrong, this package will respond with a 200 to webhook requests. Sending a 200 will prevent Conekta from resending the same event again.

There are two ways this package enables you to handle webhook requests: you can opt to queue a job or listen to the events the package will fire.

## Handling webhook requests using jobs

``` php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Yorchi\LaravelConektaWebhooks\ConektaWebhookCall;

class HandleCreatedCharge implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /** @var  \Yorchi\LaravelConektaWebhooks\ConektaWebhookCalll */
    public $webhookCall;

    public function __construct(ConektaWebhookCalll $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle()
    {
        // do your work here

        // you can access the payload of the webhook call with $this->webhookCall->payload
    }
}
```

We highly recommend that you make this job queueable, because this will minimize the response time of the webhook requests. This allows you to handle more oh dear webhook requests and avoid timeouts.

After having created your job you must register it at the jobs array in the conekta-webhooks.php config file. The key should be the name of the conekta event type. The value should be the fully qualified classname.

``` php
// config/conekta-webhooks.php

'jobs' => [
    'chargeCreated' => \App\Jobs\ConektaWebhooks\HandleCreatedCharge::class,
],
```

_*Note*_: The event type who Conekta send out is `charge.created`, all the event types, qill be converted to camelCase strings.

## Handling webhook requests using events

Instead of queueing jobs to perform some work when a webhook request comes in, you can opt to listen to the events this package will fire. Whenever a valid request hits your app, the package will fire a conekta-webhooks::<name-of-the-event> event.

The payload of the events will be the instance of ConektaWebhookCalll that was created for the incoming request.

Let's take a look at how you can listen for such an event. In the EventServiceProvider you can register listeners.


``` php
/**
 * The event listener mappings for the application.
 *
 * @var  array
 */
protected $listen = [
    'conekta-webhooks::chargeCreated' => [
        App\Listeners\MailOperators::class,
    ],
];
```

Here's an example of such a listener:

``` php
namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Yorchi\LaravelConektaWebhooks\ConektaWebhookCall;

class MailOperators implements ShouldQueue
{
    public function handle(ConektaWebhookCalll $webhookCall)
    {
        // do your work here

        // you can access the payload of the webhook call with `$webhookCall->payload`
    }
}
```

We highly recommend that you make the event listener queueable, as this will minimize the response time of the webhook requests. This allows you to handle more Oh Dear webhook requests and avoid timeouts.

## Using the ConektaWebhookCalll

Like mentioned above your events or jobs will receive an instance of `Yorchi\LaravelConektaWebhooks\ConektaWebhookCall`

You can access the raw payload by calling:

``` php
$webhookCall->payload; // returns an array;
```

Or you can opt to get more specific information:

``` php
$webhookCall->rawType(); // returns the type of the webhook (eg: 'charge.created');
$webhookCall->type(); // returns the parsed type of the webhook (eg: 'chargeCreated');
$webhookCall->data(); // returns an array with all the data of the event;
$webhookCall->object(); // returns an array with all the attribute of the current object (eg: 'charge');
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email j.andrade.dev@gmail.com instead of using the issue tracker.

## Credits

- [Jorge Andrade](https://github.com/yorchi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).