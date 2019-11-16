<?php

declare(strict_types=1);

namespace Appocular\Keeper\Http\Controllers;

use Appocular\Keeper\TestCase;

class ImageStoreControllerTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        // Set up a shared token.
        \putenv('SHARED_TOKEN=SharedToken');
    }

    protected function postImage(string $image, string $token = 'SharedToken'): void
    {
        $headers = [
            'Content-Type' => 'image/png',
            'Authorization' => 'Bearer ' . $token,
        ];
        $server = $this->transformHeadersToServerVars($headers);

        $this->call('POST', '/image', [], [], [], $server, $image);
    }

    // Test that storing image works.
    public function testStoringImage(): void
    {
        $image = \file_get_contents(__DIR__ . '/../../../fixtures/images/basn2c08.png');
        $this->postImage($image);
        $this->assertResponseStatus(201);
        $this->assertEquals(
            'http://localhost/image/240e7948f07080dfe9671daa320bbb6e4e18ced5ff2d95e89bf59ce6784963bd',
            $this->response->headers->get('location'),
        );
    }

    // Test that shared token access control works.
    public function testInvalidToken(): void
    {
        $image = \file_get_contents(__DIR__ . '/../../../fixtures/images/basn2c08.png');
        $this->postImage($image, 'BadToken');
        $this->assertResponseStatus(401);
    }

    // Test getting image.
    public function testGettingImage(): void
    {
        // Make sure image exist.
        $this->testStoringImage();
        $this->get('/image/240e7948f07080dfe9671daa320bbb6e4e18ced5ff2d95e89bf59ce6784963bd');
        $this->assertResponseStatus(200);
    }
}
