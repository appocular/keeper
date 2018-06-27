<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IntegrationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testStoringImage()
    {
        Storage::fake('public');

        $image = file_get_contents(__DIR__ . '/../../fixtures/images/basn6a16.png');
        $response = $this->json('POST', '/api/image', ['image' => base64_encode($image)]);

        $response->assertExactJson(["sha" => "3a14fed556280d45d1542e9723d3cc62326c3777"]);

        $response->assertStatus(200);
    }
}
