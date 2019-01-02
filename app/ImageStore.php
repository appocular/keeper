<?php

namespace Appocular\Keeper;

use Illuminate\Contracts\Filesystem\Cloud as Filesystem;
use Appocular\Keeper\Exceptions\InvalidImageException;
use RuntimeException;

class ImageStore
{

    /**
     * @var Filesystem
     */
    protected $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    public function store($imageData) : string
    {
        $pngData = $this->cleanPng($imageData);
        $sha = hash('sha1', $pngData);

        if (!$this->fs->put($sha . '.png', $pngData)) {
            throw new RuntimeException('Could not write file.');
        }

        return $sha;
    }

    public function retrive($sha)
    {
        $filename = $sha . '.png';
        if ($this->fs->exists($filename)) {
            return $this->fs->get($filename);
        }
        throw new RuntimeException('File does not exist.');
    }

    protected function cleanPng($imageData)
    {
        // Suppress warnings from imagecreatefromstring.
        $image = @imagecreatefromstring($imageData);
        if (!$image) {
            throw new InvalidImageException('Invalid image data.');
        }
        $tempFile = fopen("php://temp", 'r+');

        imagepng($image, $tempFile);
        imagedestroy($image);

        // Read what we have written.
        rewind($tempFile);
        $pngData = stream_get_contents($tempFile);
        fclose($tempFile);

        return $pngData;
    }
}
