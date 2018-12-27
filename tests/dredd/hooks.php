<?php

use Dredd\Hooks;

Hooks::beforeEach(function (&$transaction) {
    // Replace PNG data placeholder with real PNG data.
    if (trim($transaction->request->body) == '<PNG image data>') {
        $pngData = file_get_contents(__DIR__ . '/../../fixtures/images/basn6a16.png');
        $transaction->request->body = base64_encode($pngData);
        $transaction->request->bodyEncoding = 'base64';
    }
});

Hooks::afterEach(function (&$transaction) use (&$stash) {
    // Check that the JSON payload matches the documentation.
    if (!empty($transaction->expected->body)) {
        if (!empty($transaction->real->body)) {
            $actual = json_encode(array_sort_recursive(
                json_decode($transaction->real->body, true)
            ));
        } else {
            // No body, we'll compare with an empty result.
            $actual = json_encode([]);
        }
        $expected = array_sort_recursive(
            json_decode($transaction->expected->body, true)
        );
        $expected = json_encode($expected);

        if ($actual != $expected) {
            $transaction->fail = "Difference in JSON payload.";
        }
    }
});
