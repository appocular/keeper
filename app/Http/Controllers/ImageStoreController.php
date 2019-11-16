<?php

declare(strict_types=1);

namespace Appocular\Keeper\Http\Controllers;

use Appocular\Keeper\Exceptions\InvalidImage;
use Appocular\Keeper\ImageStore;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller;
use Laravel\Lumen\Routing\UrlGenerator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageStoreController extends Controller
{

    /**
     * The image store.
     *
     * @var \Appocular\Keeper\ImageStore
     */
    protected $imageStore;

    public function __construct(ImageStore $imageStore)
    {
        $this->imageStore = $imageStore;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(Request $request, UrlGenerator $urlGenerator): Response
    {
        $image = $request->getContent();

        try {
            $id = $this->imageStore->store($image);
        } catch (InvalidImage $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch (Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }

        return \response('', 201)->header('Location', $urlGenerator->to('/image', $id));
    }

    /**
     * Display the specified resource.
     */
    public function get(string $id): Response
    {
        try {
            $image = $this->imageStore->retrive($id);

            return \response($image, 200, ['Content-Type' => 'image/png']);
        } catch (Exception $e) {
            throw new NotFoundHttpException('Not found.');
        }
    }
}
