<?php

declare(strict_types=1);

namespace Appocular\Keeper;

use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase as LumenTestCase;

abstract class TestCase extends LumenTestCase
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }
}
