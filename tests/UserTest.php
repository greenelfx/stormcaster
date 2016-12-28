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
     * Test account disconnect
     *
     * @return void
    */
    public function testDisconnect()
    {
        $this->get('api/user/disconnect', ['HTTP_Authorization' => 'Bearer: ' . $this->getToken()])
            ->see('{"status":"success","message":"Successfully disconnected account"}');
    }

    /**
     * Test account disconnect
     *
     * @return void
    */
    public function testPasswordChange()
    {
        $faker = Faker\Factory::create();
        $username = str_random(10);
        $password = "hello1";
        $rand_new_pass = str_random(12);
        $email = $faker->email;
        $response = $this->call('POST', 'api/user/register', ['password' => $password, 'email' => $email, 'verify_password' => $password, 'name' => $username]);
        $token = json_decode($response->getContent(), true)['token'];
        $this->post('api/user/update', [], ['HTTP_Authorization' => 'Bearer: ' . $token])
            ->see('{"message":"validation","errors":{"password":["The password field is required."],"new_password":["The new password field is required."],"new_verify_password":["The new verify password field is required."]}}');
        $this->post('api/user/update', ['password' => 'hello1', 'new_password' => $rand_new_pass, 'new_verify_password' => $rand_new_pass], ['HTTP_Authorization' => 'Bearer: ' . $token])
            ->see('{"message":"success"}');
    }
}