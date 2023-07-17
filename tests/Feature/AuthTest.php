<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\base\BaseTestCase;
use App\Models\User;

class AuthTest extends BaseTestCase
{
    use RefreshDatabase, WithFaker;

    public function test_register_user_with_valid_data()
    {
        $password = $this->faker->regexify('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W])[a-zA-Z\d\W]{10,}$/');

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

    /**
     * Test successful logout.
     *
     * @return void
     */
    public function test_successful_logout()
    {
        // create a user and authenticate using Passport
        $user = User::create(
            [
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'password' => $this->faker->regexify('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W])[a-zA-Z\d\W]{8,}$/')
            ]
        );

        $token = $user->createToken('API Token')->accessToken;

        // make a request to log out
        $response = $this->post('/api/logout', [], ["Authorization" => "Bearer $token", "Accept" => "application/json"]);

        // assert that the response has a successful status code
        $response
            ->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);;
    }

    /**
     * Test successful get me information.
     *
     * @return void
     */
    public function test_successful_get_me_info()
    {
        // create a user and authenticate using Passport
        $user = User::create(
            [
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'password' => $this->faker->regexify('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W])[a-zA-Z\d\W]{8,}$/')
            ]
        );

        $token = $user->createToken('API Token')->accessToken;

        // make a request to me
        $response = $this->get('/api/me', ["Authorization" => "Bearer $token", "Accept" => "application/json"]);

        // assert that the response has a successful status code
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'user'
            ]);
    }
}
