<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PageController extends Controller
{
    /**
     * Get rankings page
     *
     * @return Response
     */
    public function getRankings()
    {
        $users = \App\Character::where('gm', '<', 4)->orderBy('level', 'DESC')->paginate(10);
        return $users;
    }

    /**
    * Get the news page
    *
    * @return Response
    */
    public function getNewsArchive() {
        return \App\News::all();
    }

    public function getNewsArticle(Request $request) {
        try {
            $article = \App\News::findOrFail($request->route('id'));
            return $article->first();
        }
        catch(ModelNotFoundException $e) {
            return response()->json(['error' => 'invalid_id'], 401);
        }
    }

    public function submitVote(Request $request) {
        
    }
}