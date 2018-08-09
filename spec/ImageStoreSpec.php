<?php

namespace spec\Ogle\Keeper;

use Ogle\Keeper\ImageStore;
use Ogle\Keeper\Exceptions\InvalidImageException;
use Illuminate\Contracts\Filesystem\Cloud as Filesystem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImageStoreSpec extends ObjectBehavior
{
    function it_is_initializable(Filesystem $fs)
    {
        $this->beConstructedWith($fs);
        $this->shouldHaveType(ImageStore::class);
    }

    function it_should_store_files(Filesystem $fs)
    {
        $fs->put('3a14fed556280d45d1542e9723d3cc62326c3777.png', Argument::any())
            ->willReturn(true)->shouldBeCalled();

        $this->beConstructedWith($fs);
        // Test image taken from http://www.schaik.com/pngsuite/pngsuite_bas_png.html
        $image = file_get_contents(__DIR__ . '/../fixtures/images/basn6a16.png');
        $this->store($image)->shouldReturn('3a14fed556280d45d1542e9723d3cc62326c3777');
    }

    function it_should_throw_on_write_errors(Filesystem $fs)
    {
        $fs->put('3a14fed556280d45d1542e9723d3cc62326c3777.png', Argument::any())
            ->willReturn(false)->shouldBeCalled();

        $this->beConstructedWith($fs);
        $image = file_get_contents(__DIR__ . '/../fixtures/images/basn6a16.png');
        $this->shouldThrow(\RuntimeException::class)->duringStore($image);
    }

    function it_should_throw_on_non_png_data(Filesystem $fs)
    {
        $this->beConstructedWith($fs);
        $this->shouldThrow(InvalidImageException::class)->duringStore('bad image');
    }

    function it_should_return_an_url_for_existing_file(Filesystem $fs)
    {
        $fs->exists('sha.png')->willReturn(true);
        $fs->url('sha.png')->willReturn('http://localhost/some_path/sha.png');

        $this->beConstructedWith($fs);
        $this->url('sha')->shouldReturn('http://localhost/some_path/sha.png');
    }

    function it_should_throw_on_non_existing_files(Filesystem $fs)
    {
        $fs->exists('sha.png')->willReturn(false);

        $this->beConstructedWith($fs);
        $this->shouldThrow(\RuntimeException::class)->duringUrl('sha');
    }
}
