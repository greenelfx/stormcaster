<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Models\User;
use \App\Models\Post;

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
     * Test that admin middleware blocks unauthorized users
     *
     * @return void
    */
    public function testUnauthorizedMiddleware()
    {
        $this->get('api/admin/numAccounts', ['HTTP_Authorization' => 'Bearer: ' . $this->getToken(0)])
            ->see('Unauthorized.');
    }

    /**
     * Test that admin middleware permits authorized users
     *
     * @return void
    */
    public function testAuthorizedMiddleware() {
         $this->get('api/admin/numAccounts', ['HTTP_Authorization' => 'Bearer: ' . $this->getToken(1)])
            ->dontSee('Unauthorized.');
    }

    /**
     * Test News CRUD
     *
     * @return void
    */
    public function testPostCRUD() {
        $token = $this->getToken(1);
        $title = str_random(10);
        $content = str_random(200);
        $type = rand(0, 2);
        $this->post('api/admin/post/create', [], ['HTTP_Authorization' => 'Bearer: ' . $token])
            ->see('["The title field is required.","The type field is required.","The content field is required."]');
        $this->post('api/admin/post/create', ["title" => $title, "type" => $type, "content" => $content], ['HTTP_Authorization' => 'Bearer: ' . $token]);
        $this->seeInDatabase('posts', [
            'title' => $title,
            'type' => $type,
            'content' => $content,
        ]);
        $post = Post::where('title', $title)->where('content', $content)->first();
        $content = str_random(20);
        $this->post('api/admin/post/edit', ["id" => $post->id, "title" => $title, "type" => $type, "content" => $content], ['HTTP_Authorization' => 'Bearer: ' . $token])
            ->see('{"success":"success"}');
        $this->seeInDatabase('posts', [
            'title' => $title,
            'type' => $type,
            'content' => $content,
        ]);
        $this->post('api/admin/post/edit', ["id" => $post->id + 1, "title" => $title, "type" => $type, "content" => $content], ['HTTP_Authorization' => 'Bearer: ' . $token])
            ->see('{"error":"invalid_id"}');
        $this->post('api/admin/post/edit', ["id" => $post->id], ['HTTP_Authorization' => 'Bearer: ' . $token])
            ->see('["The title field is required.","The type field is required.","The content field is required."]');
        $this->post('api/admin/post/delete', ["id" => $post->id], ['HTTP_Authorization' => 'Bearer: ' . $token])
            ->see('{"success":"success"}');
        $this->notSeeInDatabase('posts', [
            'title' => $title,
            'type' => $type,
            'content' => $content,
        ]);
        $this->post('api/admin/post/delete', ["id" => $post->id], ['HTTP_Authorization' => 'Bearer: ' . $token])
            ->see('{"error":"invalid_id"}');
    }
}