<?php

// phpcs:disable SlevomatCodingStandard.Namespaces.FullyQualifiedGlobalFunctions.NonFullyQualified

declare(strict_types=1);

use Dredd\Hooks;
use Symfony\Component\Process\Process;

Hooks::beforeEach(static function ($transaction): void {
    // Replace PNG data placeholder with real PNG data.
    if (trim($transaction->request->body) === '<PNG image data>') {
        $pngData = file_get_contents(__DIR__ . '/../../fixtures/images/basn2c08.png');
        $transaction->request->body = base64_encode($pngData);
        $transaction->request->bodyEncoding = 'base64';
    }

    // For the call expecting PNG data, fix the expectation to the normalized
    // PNG data returned by keeper.
    // phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
    if (isset($transaction->expected->body) && trim($transaction->expected->body) === '<PNG image data>') {
        // Convert the PNG like Keeper does, else the PNG data is likely to be different.
        $convert = new Process([
            'convert',
            '-define',
            'png:include-chunk=none',
            __DIR__ . '/../../fixtures/images/basn2c08.png',
            '-',
        ]);
        $convert->mustRun();

        $transaction->expected->body = base64_encode($convert->getOutput());
    }
});

Hooks::afterEach(static function ($transaction): void {
    // Check that the headers matches the documentation.
    if (!$transaction->expected->headers) {
        return;
    }

    foreach ($transaction->expected->headers as $name => $content) {
        // phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
        if ($transaction->real->headers->{strtolower($name)} !== $content) {
            $transaction->fail = "Difference in $name header payload.";
        }
    }
});
