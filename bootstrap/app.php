<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

if (env('REPORT_COVERAGE', false)) {
    $coverage = new SebastianBergmann\CodeCoverage\CodeCoverage();

    $coverage->filter()->addDirectoryToWhitelist(__DIR__ . '/../app');

    $coverage->start('api-test');

    // Save code coverage when the request ends.
    $handler = function () use ($coverage) {
        $coverage->stop();

        $writer = new \SebastianBergmann\CodeCoverage\Report\PHP();
        $writer->process($coverage, __DIR__ . '/../coverage/api.' . uniqid() . '.cov');
    };

    register_shutdown_function($handler);
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

// $app->withFacades();

// $app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Appocular\Keeper\Exceptions\Handler::class
);

// We don't need a custom CLI kernel, so use the standard one.
$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    Appocular\Keeper\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    Fideloper\Proxy\TrustProxies::class
]);

$app->routeMiddleware([
    'auth' => Appocular\Keeper\Http\Middleware\Authenticate::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(Appocular\Keeper\Providers\ImageStoreProvider::class);
$app->register(Appocular\Keeper\Providers\AuthServiceProvider::class);
$app->register(Fideloper\Proxy\TrustedProxyServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'Appocular\Keeper\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;
