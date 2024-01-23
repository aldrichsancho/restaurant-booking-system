<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)->assertJsonStructure(['token']);
    }

    public function test_user_registration_requires_valid_data()
    {
        // Missing 'name' field
        $response = $this->postJson('/api/register', [
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['name']);

        // Missing 'email' field
        $response = $this->postJson('/api/register', [
            'name' => $this->faker->name,
            'password' => 'password123',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);

        // Missing 'password' field
        $response = $this->postJson('/api/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['password']);
    }

    public function test_user_registration_requires_unique_email()
    {
        $existingUser = User::factory()->create();

        $response = $this->postJson('/api/register', [
            'name' => $this->faker->name,
            'email' => $existingUser->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login()
    {
        // Create a user for testing
        $password = 'password123';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        // Attempt to login with valid credentials
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)->assertJsonStructure(['token']);
    }

    public function test_user_login_requires_valid_credentials()
    {
        // Create a user for testing
        $user = User::factory()->create();

        // Attempt to login with invalid password
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'invalid_password',
        ]);

        $response->assertStatus(401)->assertSimilarJson([
            'status' => 401,
            'message' => 'Invalid credentials'
        ]);

        // Attempt to login with invalid email
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)->assertSimilarJson([
            'status' => 401,
            'message' => 'Invalid credentials'
        ]);
    }
}
