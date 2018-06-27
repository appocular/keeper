<?php

namespace Oogle\Keeper\Providers;

use Oogle\Keeper\ImageStore;
use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\FilesystemManager;

class ImageStoreProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ImageStore::class, function ($app) {
            return new ImageStore($app['filesystem']->disk('public'));
        });
    }
}
