<?php

declare(strict_types=1);

namespace Appocular\Keeper\Providers;

use Appocular\Keeper\ImageStore;
use Illuminate\Support\ServiceProvider;

class ImageStoreProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        $this->app->singleton(ImageStore::class, static function ($app): ImageStore {
            return new ImageStore($app['filesystem']->disk('public'), $app['log']);
        });
    }
}
