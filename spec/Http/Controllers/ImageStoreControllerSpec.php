<?php

namespace spec\Appocular\Keeper\Http\Controllers;

use Appocular\Keeper\Exceptions\InvalidImageException;
use Appocular\Keeper\Http\Controllers\ImageStoreController;
use Appocular\Keeper\ImageStore;
use Exception;
use Illuminate\Http\Request;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageStoreControllerSpec extends ObjectBehavior
{

    function it_should_return_sha_when_storing_image(ImageStore $store, Request $request)
    {
        $store->store(Argument::any())->willReturn('the sha');

        $request->getContent()->willReturn('image data');
        $this->beConstructedWith($store);
        $this->create($request)->shouldReturnResponse(response()->json(['sha' => 'the sha']));
    }

    function it_should_return_400_on_bad_image(ImageStore $store, Request $request)
    {
        $store->store(Argument::any())->willThrow(new InvalidImageException('bad image'));

        $request->getContent()->willReturn('image data');
        $this->beConstructedWith($store);
        $this->shouldThrow(new BadRequestHttpException('bad image'))->duringCreate($request);
    }

    function it_should_return_500_on_internal_errors(ImageStore $store, Request $request)
    {
        $store->store(Argument::any())->willThrow(new Exception('bad stuff'));

        $request->getContent()->willReturn('image data');
        $this->beConstructedWith($store);
        $this->shouldThrow(new HttpException(500, 'bad stuff'))->duringCreate($request);
    }

    function it_should_return_image(ImageStore $store)
    {
        $store->retrive('the sha')->willReturn('the data');
        $this->beConstructedWith($store);
        $this->get('the sha')->shouldReturnResponse(response('', 200)->setContent('the data'));
    }

    function it_should_return_a_404_for_unknown_images(ImageStore $store)
    {
        $store->retrive('the sha')->willThrow(new Exception());
        $this->beConstructedWith($store);
        $this->shouldThrow(new NotFoundHttpException('Not found.'))->duringGet('the sha');
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
