<?php

namespace spec\Appocular\Keeper\Http\Controllers;

use Appocular\Keeper\Exceptions\InvalidImageException;
use Appocular\Keeper\Http\Controllers\ImageStoreController;
use Appocular\Keeper\ImageStore;
use Exception;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\UrlGenerator;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageStoreControllerSpec extends ObjectBehavior
{
    function it_should_return_location_when_storing_image(ImageStore $store, UrlGenerator $urlGenerator, Request $request)
    {
        $store->store(Argument::any())->willReturn('the_hash');
        $urlGenerator->to('/image', 'the_hash')->willReturn('http://host/image/the_hash');

        $this->beConstructedWith($store);
        $expected = response('', 201)->header('Location', 'http://host/image/the_hash');
        $this->create($request, $urlGenerator)->shouldReturnResponse($expected);
    }

    function it_should_return_400_on_bad_image(ImageStore $store, UrlGenerator $urlGenerator, Request $request)
    {
        $store->store(Argument::any())->willThrow(new InvalidImageException('bad image'));

        $request->getContent()->willReturn('image data');
        $this->beConstructedWith($store);
        $this->shouldThrow(new BadRequestHttpException('bad image'))->duringCreate($request, $urlGenerator);
    }

    function it_should_return_500_on_internal_errors(ImageStore $store, UrlGenerator $urlGenerator, Request $request)
    {
        $store->store(Argument::any())->willThrow(new Exception('bad stuff'));

        $request->getContent()->willReturn('image data');
        $this->beConstructedWith($store);
        $this->shouldThrow(new HttpException(500, 'bad stuff'))->duringCreate($request, $urlGenerator);
    }

    function it_should_return_image(ImageStore $store)
    {
        $store->retrive('the hash')->willReturn('the data');
        $this->beConstructedWith($store);
        $expected = response('', 200)->header('Content-Type', 'image/png')->setContent('the data');
        $this->get('the hash')->shouldReturnResponse($expected);
    }

    function it_should_return_a_404_for_unknown_images(ImageStore $store)
    {
        $store->retrive('the hash')->willThrow(new Exception());
        $this->beConstructedWith($store);
        $this->shouldThrow(new NotFoundHttpException('Not found.'))->duringGet('the hash');
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

                $subject->headers->remove('Date');
                $expected->headers->remove('Date');
                if ($subject->headers != $expected->headers) {
                    throw new FailureException(sprintf(
                        'Response headers "%s" does not match expected "%s".',
                        $subject->headers,
                        $expected->headers
                    ));
                }

                return true;
            }
        ];
    }
}
