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
        $this->post('api/user/register', ['email' => $email])
            ->see('["The name field is required.","The password field is required.","The verify password field is required."]');
        $this->post('api/user/register', ['password' => $password])
            ->see('["The name field is required.","The verify password field is required.","The email field is required."]');
        $this->post('api/user/register', ['password' => $password, 'email' => $email, 'verify_password' => $password . "#", 'name' => $username])
            ->see('["The verify password and password must match."]');
        $this->post('api/user/register', ['password' => $password, 'email' => $email, 'verify_password' => $password, 'name' => $username])
            ->seeJsonStructure([
                 'token',
            ]);
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
        // echo $response->getContent();
        $this->post('api/user/auth', ['loginfield' => $email, 'password' => $password])
            ->seeJsonStructure(['token']);
        $this->post('api/user/auth', ['loginfield' => $username, 'password' => $password])
            ->seeJsonStructure(['token']);
        $this->post('api/user/auth', [])
            ->see('["The loginfield field is required.","The password field is required."]');
        $this->post('api/user/auth', ['loginfield' => $email, 'password' => $password . "#"])
            ->seeJsonEquals([
                 'error' => 'invalid_credentials'
             ]);
    }
}