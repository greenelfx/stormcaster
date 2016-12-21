<?php

namespace App\Http\Controllers\API;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Post;
use App\Models\Character;
use App\Models\User;

class PageController extends Controller
{
    /**
     * Get rankings page
     *
     * @return JSON of top 10 characters with pagination information
     */
    public function getRankings()
    {
        $users = Character::select('id', 'level', 'name', 'exp', 'job', 'fame', 'gm')->where('gm', '<', 4)->orderBy('level', 'DESC')->paginate(10);
        return $users;
    }

    /**
     * Get the news page
     *
     * @return JSON of all articles
     */
    public function getNewsArchive() {
        return ['message' => 'success', 'data' => Post::all()];
    }

    /**
     * Get a specific news article
     * @param id
     * @return JSON article
     */
    public function getNewsArticle(Request $request, Post $post) {
        return ['message' => 'success', 'data' => $post->first()];
    }

    /**
     * Get the online count
     *
     * @return count of online accounts
     */
    public function getOnlineCount() {
    	return ['count' => User::where('loggedin', 1)->count()];
    }

    public function submitVote(Request $request)
    {
        $credentials = $request->only('name');
        $validator = Validator::make($credentials, [
            'name' => 'required|max:127',
        ]);
        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $user = User::where('name', '=', $request->input('name'));
        if($user->count() == 1) {
            $user = $user->first();
            return response()->json(['success' => 'success'], 200);
        }
        return response()->json(['error' => 'invalid_name'], 401);
    }
}