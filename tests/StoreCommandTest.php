<?php

namespace Commands;

use Appocular\Assessor\Repo;
use Illuminate\Contracts\Filesystem\Cloud as Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Prophecy\Argument;

// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
class StoreCommandTest extends \TestCase
{
    public function testAddingImage()
    {
        $fs = $this->prophesize(Filesystem::class);
        $fs->put('859502a22c2917698b5adf7c8a52d210b5ff7c32.png', Argument::any())
            ->willReturn('the id')->shouldBeCalled();
        $fsManager = $this->prophesize(FilesystemManager::class);
        $fsManager->disk('public')->willReturn($fs)->shouldBeCalled();
        $this->app->instance('filesystem', $fsManager->reveal());

        $this->artisan('keeper:store', ['file' => 'fixtures/images/basn2c08.png']);
    }
}
