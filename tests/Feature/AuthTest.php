<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test: Poder registrar usuario
     */
    public function test_registrar_usuarios()
    {
        
        $userData = [
            "name" => "Test Usuarios",
            "email" => Str::random(10) . "@example.com",
            "password" => "12345678",
            "password_confirmation" => "12345678"
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(200)
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
            'email' => $userData['email']
        ]);
    }

    /**
     * Test: Poder iniciar sesión
     */
    public function test_login_usuario()
    {
        // Crear usuario de prueba
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('12345678')
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => '12345678'
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token'
            ])
            ->assertJson([
                'user' => [
                    'email' => $user->email,
                ]
            ]);
    }

   /**
 * Test: Cerrar sesión
 */
public function test_logout_usuario()
{
    // Crear usuario y autenticarlo
    $user = User::factory()->create([
        'password' => Hash::make('12345678')
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    // Hacer petición de logout con el token
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Sesión cerrada correctamente.'  // ← Cambiado aquí
        ]);

    // Verificar que el token fue eliminado
    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'tokenable_type' => User::class,
    ]);
    }
    /**
     * Test: Login con credenciales incorrectas
     */
    public function test_login_credenciales_incorrectas()
    {
        $loginData = [
            'email' => 'noexiste@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(401);
    }
    /**
 * Test: Login con email que no existe
 */
public function test_login_email_no_existe()
{
    $loginData = [
        'email' => 'noregistrado@example.com',
        'password' => '12345678'
    ];

    $response = $this->postJson('/api/login', $loginData);

    $response->assertStatus(401);
}

/**
 * Test: Login con contraseña incorrecta
 */
public function test_login_password_incorrecta()
{
    // Crear usuario de prueba
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('12345678')
    ]);

    $loginData = [
        'email' => 'test@example.com',
        'password' => 'wrongpassword'
    ];

    $response = $this->postJson('/api/login', $loginData);

    $response->assertStatus(401);
}
}
