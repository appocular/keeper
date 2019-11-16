<?php

declare(strict_types=1);

namespace Appocular\Keeper\Console\Commands;

use Appocular\Keeper\TestCase;
use Illuminate\Contracts\Filesystem\Cloud as Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Prophecy\Argument;

class StoreCommandTest extends TestCase
{
    public function testAddingImage(): void
    {
        $fs = $this->prophesize(Filesystem::class);
        $fs->put('240e7948f07080dfe9671daa320bbb6e4e18ced5ff2d95e89bf59ce6784963bd.png', Argument::any())
            ->willReturn('the id')->shouldBeCalled();
        $fsManager = $this->prophesize(FilesystemManager::class);
        $fsManager->disk('public')->willReturn($fs)->shouldBeCalled();
        $this->app->instance('filesystem', $fsManager->reveal());

        $this->artisan('keeper:store', ['file' => 'fixtures/images/basn2c08.png']);
    }
}
