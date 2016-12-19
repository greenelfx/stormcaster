<?php

namespace App\Http\Controllers\API;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\News;
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
        $users = Character::where('gm', '<', 4)->orderBy('level', 'DESC')->paginate(10);
        return $users;
    }

    /**
     * Get the news page
     *
     * @return JSON of all articles
     */
    public function getNewsArchive() {
        return News::all();
    }

    /**
     * Get a specific news article
     * @param id
     * @return JSON article
     */
    public function getNewsArticle(Request $request) {
        try {
            $article = News::findOrFail($request->id);
            return $article->first();
        }
        catch(ModelNotFoundException $e) {
            return response()->json(['error' => 'invalid_id'], 401);
        }
    }

    /**
     * Get the online count
     *
     * @return count of online accounts
     */
    public function getOnlineCount() {
    	return ['count' => User::where('loggedin', 1)->count()];
    }
}