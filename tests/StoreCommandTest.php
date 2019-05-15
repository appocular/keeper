<?php

namespace Commands;

use Appocular\Assessor\Repo;
use Illuminate\Contracts\Filesystem\Cloud as Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Prophecy\Argument;

// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
class AddRepoCommandTest extends \TestCase
{

    public function testAddingImage()
    {
        $fs = $this->prophesize(Filesystem::class);
        $fs->put('3a14fed556280d45d1542e9723d3cc62326c3777.png', Argument::any())->willReturn('the id')->shouldBeCalled();
        $fsManager = $this->prophesize(FilesystemManager::class);
        $fsManager->disk('public')->willReturn($fs)->shouldBeCalled();
        $this->app->instance('filesystem', $fsManager->reveal());

        $this->artisan('keeper:store', ['file' => 'fixtures/images/basn6a16.png']);
    }
}
