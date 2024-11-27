<?php

namespace Tests\Feature;

use App\Models\Notebook;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotebookTest extends TestCase
{
    use DatabaseTransactions, HasFactory;

    public function test_to_see_paginated_notebooks()
    {
        Notebook::factory(30)->create();

        $response = $this->getJson(route('notebook.index'));

        $response->assertOk();

        $this->assertCount(20, $response->json('data'));
        $this->assertEquals(30, Notebook::query()->count());
    }


    public function test_to_see_each_notebook()
    {
        $notebook = Notebook::factory()->create();

        $this->getJson(route('notebook.show', 999))->assertNotFound();
        $this->getJson(route('notebook.show', $notebook->id))->assertOk();
    }

    public function test_to_create_new_notebook()
    {
        Storage::fake('public');

        $invalidData = [
            'phone' => '887977212',
            'email' => 'test@gmail.com',
            'company' => 'This is a test company.',
            'image' => UploadedFile::fake()->image('test-image.jpg'),
        ];

        $this->postJson(route('notebook.store'), $invalidData)
            ->assertUnprocessable();

        $validData = [
            'full_name' => 'Testov Test',
            'phone' => '887977212',
            'email' => 'test@gmail.com',
            'company' => 'This is a test company.',
            'image' => UploadedFile::fake()->image('test-image.jpg'),
        ];

        $this->postJson(route('notebook.store'), $validData)
            ->assertCreated();

        $this->assertDatabaseHas('notebooks', [
            'full_name' => 'Testov Test',
            'phone' => '887977212',
            'email' => 'test@gmail.com',
            'company' => 'This is a test company.',
        ]);
    }

    public function test_to_delete_existing_notebook()
    {
        $notebook = Notebook::factory()->create();

        $this->deleteJson(route('notebook.destroy', 99999))->assertNotFound();

        $this->deleteJson(route('notebook.destroy', $notebook->id))->assertNoContent();
    }

    public function test_to_update_existing_notebook()
    {
        Storage::fake('public');

        $notebook = Notebook::query()->create([
            'full_name' => 'Old Name',
            'phone' => '123456789',
            'email' => 'oldemail@gmail.com',
            'company' => 'Old Company',
            'image' => 'images/existing_image.jpg',
        ]);

        Storage::disk('public')->put('images/existing_image.jpg', 'fake-content');


        $newData = [
            'full_name' => 'Updated Name',
            'phone' => '321654987',
            'email' => 'updatedemail@gmail.com',
            'company' => 'Updated Company',
        ];

         $this->putJson(route('notebook.update', 9999), $newData)->assertNotFound();
         $this->putJson(route('notebook.update', $notebook->id), $newData)->assertOk();


        $this->assertDatabaseHas('notebooks', [
            'full_name' => 'Updated Name',
            'phone' => '321654987',
            'email' => 'updatedemail@gmail.com',
            'company' => 'Updated Company',
            'image' => 'images/existing_image.jpg',
        ]);


    }
}
