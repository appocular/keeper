<?php

namespace Appocular\Keeper\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Appocular\Keeper\ImageStore;
use Appocular\Keeper\Exceptions\InvalidImageException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ImageStoreController extends Controller
{

    /**
     * @var ImageStore
     */
    protected $imageStore;

    public function __construct(ImageStore $imageStore)
    {
        $this->imageStore = $imageStore;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $image = $request->getContent();
        try {
            $sha = $this->imageStore->store($image);
        } catch (InvalidImageException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch (Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
        return response()->json(['sha' => $sha]);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        try {
            $image = $this->imageStore->retrive($id);
            return response($image, 200, ['Content-Type' => 'image/png']);
        } catch (Exception $e) {
            throw new NotFoundHttpException('Not found.');
        }
    }
}
