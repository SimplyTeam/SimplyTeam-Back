<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_register_user_with_valid_data()
    {
        $password = $this->faker->password(8);

        $data = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'user',
                     'access_token'
                 ]);
    }

    public function test_register_user_with_missing_name()
    {
        $password = $this->faker->password(8);

        $data = [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_register_user_with_invalid_email()
    {
        $password = $this->faker->password(8);

        $data = [
            'name' => $this->faker->name(),
            'email' => 'invalid email',
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_register_user_with_invalid_passwords()
    {
        $invalidPasswords = [
            'password',         // ne contient pas de caractère spécial
            'Password',         // ne contient pas de chiffre
            'password1',        // ne contient pas de caractère spécial en plus du chiffre
            'Password!',        // ne contient pas de minuscule
            'password!',        // ne contient pas de majuscule
            'PASSWORD!',        // ne contient pas de minuscule
            'Password1',        // ne contient pas de caractère spécial
            'Pa!3wor',             // trop court (moins de 8 caractères)
        ];

        foreach ($invalidPasswords as $password) {
            $response = $this->postJson('/api/register', [
                'name' => $this->faker->name(),
                'email' => $this->faker->email(),
                'password' => $password,
                'password_confirmation' => $password,
            ]);

            $response
                ->assertStatus(422)
                ->assertJsonValidationErrors('password');
        }
    }

    function test_login_doesn_t_work_with_invalid_credential(){
        $response = $this->postJson('/api/login', [
            'email' => $this->faker->email(),
            'password' => $this->faker->password()
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('message');
    }

    function test_login_work_with_valid_credential(){
        $email = $this->faker->unique()->safeEmail();
        $password = "passW0rd!123";

        var_dump($password);

        $data = [
            'name' => $this->faker->name(),
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user',
                'access_token'
            ]);

        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => $password
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'user',
                'access_token'
            ]);
    }
}
