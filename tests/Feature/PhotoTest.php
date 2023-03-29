<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PhotoTest extends TestCase
{
    use WithFaker;

    /**
     * Test upload photo and query.
     */
    public function test_upload_photo_and_query(): void
    {
        $file = UploadedFile::fake()->image('avatar.jpg');
        $token = $this->post(route('upload'), [
            'file' => $file,
        ])->assertStatus(201)->json('token');

        $response = $this->postJson(route('query'), [
            'token' => $token,
        ])->assertStatus(200);

        $this->assertFileExists(storage_path('app/public/' . $response->json('path')));
    }
}
