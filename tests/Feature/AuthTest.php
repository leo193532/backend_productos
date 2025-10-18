<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use Illuminate\Support\Str;


class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_registrar_usuarios(){
        $userData = [
            "name" => "test Usuarios",
            "email" => Str::random(5) . "@example.com",
            "password" => "12345678",
            "password_confirmation" => "12345678"
        ];

        $response = $this->postJson('/api/register', $userData);

        $response-> assertStatus(200)
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'created_at'],
            'token'  
        ])
        ->assertJson([
            'user' => [
                'name' => $userData['name'],
                'email' => $userData['email'],
            ]
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);
    }
}