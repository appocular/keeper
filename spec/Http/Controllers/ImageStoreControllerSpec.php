<?php

namespace spec\Ogle\Keeper\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Ogle\Keeper\Exceptions\InvalidImageException;
use Ogle\Keeper\Http\Controllers\ImageStoreController;
use Ogle\Keeper\ImageStore;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Laravel\LaravelObjectBehavior;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImageStoreControllerSpec extends LaravelObjectBehavior
{

    function it_should_return_sha_when_storing_image(ImageStore $store, Request $request)
    {
        $store->store(Argument::any())->willReturn('the sha');

        $request->json('image')->willReturn('image data');
        $this->beConstructedWith($store);
        $this->create($request)->shouldReturnResponse(response()->json(['sha' => 'the sha']));
    }

    function it_should_return_400_on_bad_image(ImageStore $store, Request $request)
    {
        $store->store(Argument::any())->willThrow(new InvalidImageException('bad image'));

        $request->json('image')->willReturn('image data');
        $this->beConstructedWith($store);
        $this->create($request)->shouldReturnResponse(response()->json(['error' => 'bad image'], 400));
    }

    function it_should_return_500_on_internal_errors(ImageStore $store, Request $request)
    {
        $store->store(Argument::any())->willThrow(new Exception('bad stuff'));

        $request->json('image')->willReturn('image data');
        $this->beConstructedWith($store);
        $this->create($request)->shouldReturnResponse(response()->json(['error' => 'bad stuff'], 500));
    }

    function getMatchers() : array
    {
        return [
            'returnResponse' => function ($subject, $expected) {
                if ($subject->getStatusCode() != $expected->getStatusCode()) {
                    throw new FailureException(sprintf(
                        'Response with code %s does not match expected %s.',
                        $subject->getStatusCode(),
                        $expected->getStatusCode()
                    ));
                }

                if ($subject->getContent() != $expected->getContent()) {
                    throw new FailureException(sprintf(
                        'Response with content "%s" does not match expected "%s".',
                        $subject->getContent(),
                        $expected->getContent()
                    ));
                }

                return true;
            }
        ];
    }
}
