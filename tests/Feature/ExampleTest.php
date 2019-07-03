<?php

namespace Tests\Feature;

use App\Image;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_auth_user(){
        $this->get('/uploadImage')->assertRedirect('/login');
    }

    public function test_index(){

        $user = factory(User::class)->create();
        $this->actingAs($user);

        Image::create([
            'user_id' => $user->id,
            'path' => 'dfsdf',
            'type' => 'fff',
            'size' => '1000',
            'filename' => 'ddd',
        ]);

        $this->actingAs($user);
        $response = $this->get('/uploadImage');

        $content = json_decode($response->getContent(), true);
//        dd($content['images']);

        $response->assertJson([
            'images' =>  $content['images'],
            'message' => true,
        ]);
        $response->assertStatus(200);

    }

    public function test_upload_validate_wrong_file(){

        Storage::fake('public');
        $image = UploadedFile::fake()->image('test.txt');
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->json('POST', '/uploadImage', [
            'file' => $image,
        ]);

        $content = json_decode($response->getContent(), true);

        foreach ($content['errors']['file'] as $error){
            $response->assertSeeText($error);
        }

    }

    public function test_upload_validate_wrong_size(){

        Storage::fake('public');
        $image = UploadedFile::fake()->image('test.jpeg')->size(40000);
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->json('POST', '/uploadImage', [
            'file' => $image,
        ]);

        $content = json_decode($response->getContent(), true);

        foreach ($content['errors']['file'] as $error){
            $response->assertSeeText($error);
        }
    }



    public function test_upload_is_record_created(){
        Storage::fake('public');
        $image = UploadedFile::fake()->image('test.jpeg');
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $this->json('POST', '/uploadImage', ['file' => $image]);
//        $this->post('/uploadImage', ['file' => $image]);

        $this->assertCount(1, Image::all());
    }

    public function test_upload_image_is_image_seved(){

        Storage::fake('public');
        $image = UploadedFile::fake()->image('test.jpeg');
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->json('POST', '/uploadImage', [
            'file' => $image
        ]);
        $content = json_decode($response->getContent(),true);

        Storage::disk('public')->assertExists($content['image']['path'].$content['image']['filename']);
        $response->assertJson([
            'image' =>  $content['image'],
            'message' => $content['message'],
        ]);
        $response->assertStatus(200);
    }

    public function test_destroy_is_record_destroy(){
        Storage::fake('public');
        $image = UploadedFile::fake()->image('test.jpeg');
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $responsePost = $this->json('POST', '/uploadImage', [
            'file' => $image
        ]);
        $content = json_decode($responsePost->getContent());

        $this->json('DELETE', '/uploadImage'.'/'.$content->image->id);

        $this->assertCount(0, Image::all());
    }

    public function test_destroy_image_is_image_destroyed(){
        Storage::fake('public');
        $image = UploadedFile::fake()->image('test.jpeg');
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $responsePost = $this->json('POST', '/uploadImage', [
            'file' => $image
        ]);
        $content = json_decode($responsePost->getContent());

        $responseDelete = $this->json('DELETE', '/uploadImage'.'/'.$content->image->id);
        Storage::disk('public')->assertMissing($content->image->path.$content->image->id);
        $contentDelete = json_decode($responseDelete->getContent());
        $responseDelete->assertJson([
            'message' => $contentDelete->message,
        ]);
        $responseDelete->assertStatus(200);
    }
}
