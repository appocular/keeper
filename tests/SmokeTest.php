<?php

declare(strict_types=1);

namespace Appocular\Keeper;

class SmokeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample(): void
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(),
            $this->response->getContent(),
        );
    }
}
