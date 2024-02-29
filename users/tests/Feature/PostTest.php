<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Events\UserCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_validation_failed_response(): void
    {
        $email = 'testmail1@mail';
        $response = $this->post('/api/users', [
            'email' => $email,
            'first_name' => 'Trial',
            'last_name' => 'Bella',
        ]);

        $response->assertStatus(422)
            ->assertJson(['successful' => false]);
    }

    public function test_email_validation_failed_response(): void
    {
        $email = 'testmail1@mail';
        $response = $this->post('/api/users', [
            'email' => $email,
            'first_name' => 'Trial',
            'last_name' => 'Bella',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'successful' => false,
                'message' => 'The email field must be a valid email address.',
            ]);
    }

    public function test_first_name_validation_failed_response(): void
    {
        $email = 'testmail1@mail.com';
        $response = $this->post('/api/users', [
            'email' => $email,
            'first_name' => 10,
            'last_name' => 'Bella',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'successful' => false,
                'message' => 'The first name field must be a string.',
            ]);
    }

    public function test_last_name_validation_failed_response(): void
    {
        $email = 'testmail1@mail.com';
        $response = $this->post('/api/users', [
            'email' => $email,
            'first_name' => 'Lila',
            'last_name' => 100,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'successful' => false,
                'message' => 'The last name field must be a string.',
            ]);
    }

    public function test_post_resource_not_found_response(): void
    {
        $email = 'testmail1@mail';
        $response = $this->post('/api/', [
            'email' => $email,
            'first_name' => 'Trial',
            'last_name' => 'Bella',
        ]);

        $response->assertStatus(404)
            ->assertJson(['successful' => false]);
    }

    public function test_post_returns_a_successful_response(): void
    {
        $email = 'testmail1@mail.com';
        $response = $this->post('/api/users', [
            'email' => $email,
            'first_name' => 'Trial',
            'last_name' => 'Bella',
        ]);

        $response->assertStatus(200)
            ->assertJson(['successful' => true]);
    }

    public function test_post_data_created_in_database(): void
    {
        $data = [
            'email' => 'testmail1@mail.com',
            'first_name' => 'Trial',
            'last_name' => 'Bella',
        ];

        $this->post('/api/users', $data);

        $this->assertDatabaseHas('users', $data);
    }

    public function test_event_message_dispatched(): void
    {
        Event::fake();

        $data = [
            'email' => 'testmail1@mail.com',
            'first_name' => 'Trial',
            'last_name' => 'Bella',
        ];

        $this->post('/api/users', $data);

        Event::assertDispatched(UserCreated::class);

    }
}
