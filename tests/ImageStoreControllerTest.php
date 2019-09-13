<?php

// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
class ImageStoreControllerTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        // Set up a frontend token.
        \putenv('SHARED_TOKEN=SharedToken');
    }

    protected function postImage($image, $token = 'SharedToken')
    {
        $headers = [
            'Content-Type' => 'image/png',
            'Authorization' => 'Bearer ' . $token,
        ];
        $server = $this->transformHeadersToServerVars($headers);

        $this->call('POST', '/image', [], [], [], $server, $image);

        return $this;
    }

    // Test that storing image works.
    public function testStoringImage()
    {
        $image = file_get_contents(__DIR__ . '/../fixtures/images/basn2c08.png');
        $this->postImage($image);
        $this->assertResponseStatus(201);
        $this->assertEquals(
            'http://localhost/image/240e7948f07080dfe9671daa320bbb6e4e18ced5ff2d95e89bf59ce6784963bd',
            $this->response->headers->get('location')
        );
    }

    // Test that shared token access control works.
    public function testInvalidToken()
    {
        $image = file_get_contents(__DIR__ . '/../fixtures/images/basn2c08.png');
        $this->postImage($image, 'BadToken');
        $this->assertResponseStatus(401);
    }

    // Test getting image.
    public function testGettingImage()
    {
        // Make sure image exist.
        $this->testStoringImage();
        $this->get('/image/240e7948f07080dfe9671daa320bbb6e4e18ced5ff2d95e89bf59ce6784963bd');
        $this->assertResponseStatus(200);
    }
}
