<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Models\User;

class AuthTest extends TestCase
{
    /**
     * Test that sign up validation and token generation is working
     *
     * @return void
    */
    public function testValidationSignUp()
    {
        $faker = Faker\Factory::create();
        $username = str_random(10);
        $password = str_random(10);
        $email = $faker->email;
        $this->post('api/user/register', [])
            ->seeJsonEquals([
                "message" => "validation",
                "errors" => [
                    "name" => ["The name field is required."],
                    "password" => ["The password field is required."],
                    "verify_password" => ["The verify password field is required."],
                    "email" => ["The email field is required."],
                ]
            ]);
        $this->post('api/user/register', ['password' => $password, 'email' => $email, 'verify_password' => $password . "#", 'name' => $username])
            ->seeJsonEquals([
                "message" => "validation",
                "errors" => [
                    "verify_password" => ["The verify password and password must match."],
                ]
            ]);
        $this->post('api/user/register', ['password' => $password, 'email' => $email, 'verify_password' => $password, 'name' => $username])
            ->seeJsonStructure(['message', 'token']);
    }

    /**
     * Test that login works correctly
     *
     * @return void
     */
    public function testAuthentication()
    {
        $faker = Faker\Factory::create();
        $username = str_random(10);
        $password = str_random(10);
        $email = $faker->email;
        $response = $this->call('POST', 'api/user/register', ['name' => $username, 'password' => $password, 'verify_password' => $password, 'email' => $email]);
        $this->post('api/user/auth', ['loginfield' => $email, 'password' => $password])
            ->seeJsonStructure(['message', 'token']);
        $this->post('api/user/auth', ['loginfield' => $username, 'password' => $password])
            ->seeJsonStructure(['message', 'token']);
        $this->post('api/user/auth', [])
            ->seeJsonEquals([
                "message" => "validation",
                "errors" => [
                    "loginfield" => ["The loginfield field is required."],
                    "password" => ["The password field is required."],
                ]
            ]);
        $this->post('api/user/auth', ['loginfield' => $email, 'password' => $password . "#"])
            ->seeJsonEquals([
                 'message' => 'invalid_credentials'
             ]);
    }
}
