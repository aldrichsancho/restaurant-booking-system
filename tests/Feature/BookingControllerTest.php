<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    private function login_process(): array
    {
        $response = $this->postJson('/api/login', [
            'email'     => 'admin@restaurant.com',
            'password'  => 'password'
        ]);

        $token = $response->json('token');

        $response = $this->getJson('/api/profile',
            ['Authorization' => 'Bearer ' . $token]
        );

        return [
            'token' => $token,
            'id'    => $response->json('id'),
        ];
    }

    public function test_successfully_created_booking(): void
    {
        $user = $this->login_process();
        $token = $user['token'];

        $restaurant = Restaurant::where('name', 'Restaurant A')->first();

        $response = $this->postJson('/api/bookings', [
            'restaurant_id' => $restaurant->id,
            'table_id' => $restaurant->tables()->first()->id,
            'customer_name' => 'Salim Arizi',
            'start_time' => '2024-01-25 16:00:00',
            'end_time' => '2024-01-25 18:00:00',
            'is_online' => true
        ], ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Successfully created booking']);
    }

    public function test_validation_time(): void
    {
        $user = $this->login_process();
        $token = $user['token'];

        $restaurant = Restaurant::where('name', 'Restaurant A')->first();

        $response = $this->postJson('/api/bookings', [
            'restaurant_id' => $restaurant->id,
            'table_id' => $restaurant->tables()->first()->id,
            'customer_name' => 'Salim Arizi',
            'start_time' => '2024-01-25 16:00:00',
            'end_time' => '2024-01-25 14:00:00',
            'is_online' => true
        ], ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Failed to book']);
    }

    public function test_booking_should_not_double_on_the_same_time_and_same_table(): void
    {
        $user = $this->login_process();
        $token = $user['token'];

        $restaurant = Restaurant::where('name', 'Restaurant A')->first();

        $this->postJson('/api/bookings', [
            'restaurant_id' => $restaurant->id,
            'table_id' => $restaurant->tables()->first()->id,
            'customer_name' => 'Salim Arizi',
            'start_time' => '2024-01-25 16:00:00',
            'end_time' => '2024-01-25 18:00:00',
            'is_online' => true
        ], ['Authorization' => 'Bearer ' . $token]);

        $response = $this->postJson('/api/bookings', [
            'restaurant_id' => $restaurant->id,
            'table_id' => $restaurant->tables()->first()->id,
            'customer_name' => 'Salim Arizi',
            'start_time' => '2024-01-25 16:00:00',
            'end_time' => '2024-01-25 18:00:00',
            'is_online' => true
        ], ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Failed to book']);
    }
}
