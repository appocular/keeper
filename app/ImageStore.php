<?php

declare(strict_types=1);

namespace Appocular\Keeper;

use Appocular\Keeper\Exceptions\InvalidImage;
use Illuminate\Contracts\Filesystem\Cloud as Filesystem;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class ImageStore
{

    /**
     * Filesystem to store images in.
     *
     * @var \Illuminate\Contracts\Filesystem\Cloud
     */
    protected $fs;

    /**
     * Logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $log;

    public function __construct(Filesystem $fs, LoggerInterface $log)
    {
        $this->fs = $fs;
        $this->log = $log;
    }

    public function store(string $imageData): string
    {
        $this->log->debug('Cleaning PNG data');
        $pngData = $this->cleanPng($imageData);
        $this->log->debug('Hashing PNG data');
        $hash = \hash('sha256', $pngData);

        $this->log->debug(\sprintf('Saving "%s"', $hash));

        if (!$this->fs->put($hash . '.png', $pngData)) {
            throw new RuntimeException('Could not write file.');
        }

        return $hash;
    }

    public function retrive(string $sha): string
    {
        $filename = $sha . '.png';

        if ($this->fs->exists($filename)) {
            return $this->fs->get($filename);
        }

        throw new RuntimeException('File does not exist.');
    }

    protected function cleanPng(string $imageData): string
    {
        // Run convert and make it strip all non-essential chunks. The dashes
        // makes it use stdin and stdout.
        $convert = new Process(['convert', '-define', 'png:include-chunk=none', '-', '-']);
        $convert->setTimeout(600);
        $convert->setInput($imageData);

        try {
            $convert->mustRun();
        } catch (Throwable $e) {
            throw new InvalidImage('Invalid image data.');
        }

        return $convert->getOutput();
    }
}
