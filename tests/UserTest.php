<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Models\User;

class UserTest extends TestCase
{
    public function getToken() {
        $faker = Faker\Factory::create();
        $username = str_random(10);
        $password = str_random(10);
        $email = $faker->email;
        $response = $this->call('POST', 'api/user/register', ['password' => $password, 'email' => $email, 'verify_password' => $password, 'name' => $username]);
        return json_decode($response->getContent(), true)['token'];
    }

    /**
     * Test that account disconnect works
     *
     * @return void
    */
    public function testDisconnect()
    {
        $this->get('api/user/disconnect', ['HTTP_Authorization' => 'Bearer: ' . $this->getToken()])
            ->see('{"status":"success","message":"Successfully disconnected account"}');
    }
}