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
        $fs->put('3c13efad9953e33a6ff0ddf0ebaf3218cb196ae4.png', Argument::any())
            ->willReturn(true)->shouldBeCalled();

        $this->beConstructedWith($fs);
        // 3x8 bits RGB color, the most likely format we'll see.
        $image = file_get_contents(__DIR__ . '/../fixtures/images/basn2c08.png');
        $this->store($image)->shouldReturn('3c13efad9953e33a6ff0ddf0ebaf3218cb196ae4');
    }

    function it_should_throw_on_write_errors(Filesystem $fs)
    {
        $fs->put('3c13efad9953e33a6ff0ddf0ebaf3218cb196ae4.png', Argument::any())
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
        $fs->exists('sha.png')->willReturn(true);
        $fs->get('sha.png')->willReturn('image data');

        $this->beConstructedWith($fs);
        $this->retrive('sha')->shouldReturn('image data');
    }

    function it_should_throw_on_non_existing_files(Filesystem $fs)
    {
        $fs->exists('sha.png')->willReturn(false);

        $this->beConstructedWith($fs);
        $this->shouldThrow(\RuntimeException::class)->duringRetrive('sha');
    }

    function it_should_retain_the_alpha_channel_of_the_image(Filesystem $fs)
    {
        $fs->put('cb0bc88e6b66f7565f981fd9a1aaf2bd722d481a.png', Argument::any())
            ->willReturn(true)->shouldBeCalled();

        $this->beConstructedWith($fs);
        // 3x8 bits rgb color + 8 bit alpha-channel. Alpha channel is used in
        // diffs.
        $image = file_get_contents(__DIR__ . '/../fixtures/images/basn6a08.png');
        $this->store($image)->shouldReturn('cb0bc88e6b66f7565f981fd9a1aaf2bd722d481a');
    }

    /*
     * Testing with all possible PNG formats and options is overkill, but
     * throw in a test handling the highest color-depth for good measure.
     */
    function it_should_deal_with_16_bit_files(Filesystem $fs)
    {
        $fs->put('f3d08c07bce4ea64427e5698daf693c9692394a4.png', Argument::any())
            ->willReturn(true)->shouldBeCalled();

        $this->beConstructedWith($fs);
        // 3x16 bits RGB color + 16 bit alpha-channel. Note that the saved
        // file represented by the SHA is 3x8 bits RGB, with 8 bit alpha, as
        // that seems the maximum PHP will go, but 16bit colors isn't really
        // needed in our case.
        $image = file_get_contents(__DIR__ . '/../fixtures/images/basn6a16.png');
        $this->store($image)->shouldReturn('f3d08c07bce4ea64427e5698daf693c9692394a4');
    }
}
