<?php

namespace Yorchi\LaravelConektaWebhooks;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Yorchi\LaravelConektaWebhooks\Skeleton\SkeletonClass
 */
class LaravelConektaWebhooksFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-conekta-webhooks';
    }
}
