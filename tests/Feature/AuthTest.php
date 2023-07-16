<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_get_token()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "access_token",
                "token_type"
            ]);
    }

    public function test_invalid_credentials_return_error()
    {
        $response = $this->postJson('/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'invalid-password'
        ]);

        $response->assertStatus(403)
            ->assertJson([
                "message" => "Invalid credentials."
            ]);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
        ];

        $response = $this->postJson('/auth/logout', [], $headers);

        $response->assertStatus(200)
            ->assertJson([
                "status" => true,
            ]);

        $this->assertCount(0, $user->tokens);
    }

    public function test_unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson('/auth/logout');

        $response->assertStatus(401)
            ->assertJson([
                "status" => false,
                "message" => "User not authenticated.",
            ]);
    }
}
