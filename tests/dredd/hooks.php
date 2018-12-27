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
    // Check that the headers matches the documentation.
    if (!empty($transaction->expected->headers)) {
        foreach ($transaction->expected->headers as $name => $content) {
            if ($transaction->real->headers->{strtolower($name)} != $content) {
                $transaction->fail = "Difference in $name header payload.";
            }
        }
    }
    // Check that the JSON payload matches the documentation.
    if (!empty($transaction->expected->body)) {
        switch ($transaction->expected->headers->{"Content-Type"}) {
            case 'application/json':
                $actual = normalize_json($transaction->real->body);
                $expected = normalize_json($transaction->expected->body);
                break;

            default:
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
