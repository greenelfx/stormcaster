<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Models\User;

class AdminTest extends TestCase
{
    public function getToken($admin) {
        $faker = Faker\Factory::create();
        $username = str_random(10);
        $password = str_random(10);
        $email = $faker->email;
        $response = $this->call('POST', 'api/user/register', ['password' => $password, 'email' => $email, 'verify_password' => $password, 'name' => $username]);
        if($admin && $admin == 1) {
            $user = User::where('name', $username)->first();
            $user->webadmin = 1;
            $user->save();
        }
        $response = $this->call('POST', 'api/user/auth', ['password' => $password, 'loginfield' => $email]);
        return json_decode($response->getContent(), true)['token'];
    }

    /**
     * Test that account disconnect works
     *
     * @return void
    */
    public function testUnauthorizedMiddleware()
    {
        $this->get('api/admin/numAccounts', ['HTTP_Authorization' => 'Bearer: ' . $this->getToken(0)])
            ->see('Unauthorized.');
    }
    public function testAuthorizedMiddleware() {
         $this->get('api/admin/numAccounts', ['HTTP_Authorization' => 'Bearer: ' . $this->getToken(1)])
            ->dontSee('Unauthorized.');
    }
}