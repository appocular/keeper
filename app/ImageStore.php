<?php

namespace Oogle\Keeper;

use Illuminate\Filesystem\Filesystem;

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

    public function store($imageData)
    {
        $pngData = $this->cleanPng($imageData);
        $sha = hash('sha1', $pngData);

        if (!$this->fs->put($sha . '.png', $pngData)) {
            throw new \RuntimeException('Could not write file.');
        }

        return $sha;
    }

    private function cleanPng($imageData)
    {
        $image = imagecreatefromstring($imageData);
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
