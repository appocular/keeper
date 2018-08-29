<?php

namespace Ogle\Keeper\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Ogle\Keeper\ImageStore;
use Ogle\Keeper\Exceptions\InvalidImageException;

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
        $image = base64_decode($request->json('image'));
        try {
            $sha = $this->imageStore->store($image);
        } catch (InvalidImageException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
            $url = $this->imageStore->url($id);
            return response('', 302, ['Location' => $url]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Not found'], 404);
        }
    }
}
