<?php

use Dredd\Hooks;
use Symfony\Component\Process\Process;

Hooks::beforeEach(function (&$transaction) {
    // Replace PNG data placeholder with real PNG data.
    if (trim($transaction->request->body) == '<PNG image data>') {
        $pngData = file_get_contents(__DIR__ . '/../../fixtures/images/basn2c08.png');
        $transaction->request->body = base64_encode($pngData);
        $transaction->request->bodyEncoding = 'base64';
    }

    // For the call expecting PNG data, fix the expectation to the normalized
    // PNG data returned by keeper.
    if (trim($transaction->expected->body) == '<PNG image data>') {
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

Hooks::afterEach(function (&$transaction) use (&$stash) {
    // Check that the headers matches the documentation.
    if (!empty($transaction->expected->headers)) {
        foreach ($transaction->expected->headers as $name => $content) {
            if ($transaction->real->headers->{strtolower($name)} != $content) {
                $transaction->fail = "Difference in $name header payload.";
            }
        }
    }

    // Check that the payload matches the documentation.
    if (!empty($transaction->expected->body)) {
        switch ($transaction->expected->headers->{"Content-Type"}) {
            case 'application/json':
                $actual = normalize_json($transaction->real->body);
                $expected = normalize_json($transaction->expected->body);
                break;

            default:
                // Simple comparison for everything else. This includes
                // text/plain which dredd apparently checks itself, but what
                // the hell, we'll check it too.
                $actual = $transaction->real->body;
                $expected = $transaction->expected->body;
        }

        if ($actual != $expected) {
            $transaction->fail = "Difference in JSON payload.";
        }
    }
});

function normalize_json($json)
{
    if (!empty($json)) {
        return json_encode(array_sort_recursive(json_decode($json, true)));
    }
    return "";
}
