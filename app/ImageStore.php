<?php

namespace Appocular\Keeper;

use Appocular\Keeper\Exceptions\InvalidImageException;
use Illuminate\Contracts\Filesystem\Cloud as Filesystem;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

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
        // Run convert and make it strip all non-essential chunks. The dashes
        // makes it use stdin and stdout.
        $convert = new Process(['convert', '-define', 'png:include-chunk=none', '-', '-']);
        $convert->setInput($imageData);
        try {
            $convert->mustRun();
        } catch (Throwable $e) {
            throw new InvalidImageException('Invalid image data.');
        }

        return $convert->getOutput();
    }
}
