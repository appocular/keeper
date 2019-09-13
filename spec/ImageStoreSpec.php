<?php

namespace spec\Appocular\Keeper;

use Appocular\Keeper\ImageStore;
use Appocular\Keeper\Exceptions\InvalidImageException;
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
        $fs->put('240e7948f07080dfe9671daa320bbb6e4e18ced5ff2d95e89bf59ce6784963bd.png', Argument::any())
            ->willReturn(true)->shouldBeCalled();

        $this->beConstructedWith($fs);
        // 3x8 bits RGB color, the most likely format we'll see.
        $image = file_get_contents(__DIR__ . '/../fixtures/images/basn2c08.png');
        $this->store($image)->shouldReturn('240e7948f07080dfe9671daa320bbb6e4e18ced5ff2d95e89bf59ce6784963bd');
    }

    function it_should_throw_on_write_errors(Filesystem $fs)
    {
        $fs->put('240e7948f07080dfe9671daa320bbb6e4e18ced5ff2d95e89bf59ce6784963bd.png', Argument::any())
            ->willReturn(false)->shouldBeCalled();

        $this->beConstructedWith($fs);
        $image = file_get_contents(__DIR__ . '/../fixtures/images/basn2c08.png');
        $this->shouldThrow(\RuntimeException::class)->duringStore($image);
    }

    function it_should_throw_on_non_png_data(Filesystem $fs)
    {
        $this->beConstructedWith($fs);
        $this->shouldThrow(InvalidImageException::class)->duringStore('bad image');
    }

    function it_should_return_image_data_for_existing_file(Filesystem $fs)
    {
        $fs->exists('hash.png')->willReturn(true);
        $fs->get('hash.png')->willReturn('image data');

        $this->beConstructedWith($fs);
        $this->retrive('hash')->shouldReturn('image data');
    }

    function it_should_throw_on_non_existing_files(Filesystem $fs)
    {
        $fs->exists('hash.png')->willReturn(false);

        $this->beConstructedWith($fs);
        $this->shouldThrow(\RuntimeException::class)->duringRetrive('hash');
    }

    function it_should_retain_the_alpha_channel_of_the_image(Filesystem $fs)
    {
        $fs->put('41e8adba57885d3bb6c5e53596ff56db54b1ef4b1c7d2fe2e3bb39e9045fb6d6.png', Argument::any())
            ->willReturn(true)->shouldBeCalled();

        $this->beConstructedWith($fs);
        // 3x8 bits rgb color + 8 bit alpha-channel. Alpha channel is used in
        // diffs.
        $image = file_get_contents(__DIR__ . '/../fixtures/images/basn6a08.png');
        $this->store($image)->shouldReturn('41e8adba57885d3bb6c5e53596ff56db54b1ef4b1c7d2fe2e3bb39e9045fb6d6');
    }

    /*
     * Testing with all possible PNG formats and options is overkill, but
     * throw in a test handling the highest color-depth for good measure.
     */
    function it_should_deal_with_16_bit_files(Filesystem $fs)
    {
        $fs->put('3f9200a6dee485e3fbf67e68b1e9f2bbb6e48387dd1e9c676c2e0bf48feb1a98.png', Argument::any())
            ->willReturn(true)->shouldBeCalled();

        $this->beConstructedWith($fs);
        // 3x16 bits RGB color + 16 bit alpha-channel. Note that the saved
        // file represented by the HASH is 3x8 bits RGB, with 8 bit alpha, as
        // that seems the maximum PHP will go, but 16bit colors isn't really
        // needed in our case.
        $image = file_get_contents(__DIR__ . '/../fixtures/images/basn6a16.png');
        $this->store($image)->shouldReturn('3f9200a6dee485e3fbf67e68b1e9f2bbb6e48387dd1e9c676c2e0bf48feb1a98');
    }
}
