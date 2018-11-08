<?php

namespace Appocular\Keeper\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Appocular\Keeper\Events\ExampleEvent' => [
            'Appocular\Keeper\Listeners\ExampleListener',
        ],
    ];
}
