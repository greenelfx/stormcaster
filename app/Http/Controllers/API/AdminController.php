<?php

namespace App\Http\Controllers\API;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;
use App\Models\Post;
use Auth;
use JWTAuth;
use Hash;

class AdminController extends Controller
{

    /**
     * Get number of accounts
     *
     * @param  Request  $request
     * @return Response
     */
    public function getNumAccounts() {
        return User::count();
    }


    /**
     * Update a news article
     *
     * @param  Request  $request
     * @return Response
     */
    public function editNews(Request $request) {
        try {
            $article = News::findOrFail($request->input('id'));
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'type'   => 'required|string',
                'content' => 'required'
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            else {
                $article->title = $request->input('title');
                $article->type = $request->input('type');
                $article->content = $request->input('content');
                $article->save();
                return response()->json(['success' => 'success'], 200);
            }
        }
        catch(ModelNotFoundException $e) {
            return response()->json(['error' => 'invalid_id'], 401);
        }
    }
    /**
     * Delete a news article
     *
     * @param  Request  $request
     * @return Response
     */
    public function deleteNews(Request $request) {
        try {
            $article = News::findOrFail($request->input('id'));
            $article->delete();
            return response()->json(['success' => 'success'], 200);
        }
        catch(ModelNotFoundException $e) {
            return response()->json(['error' => 'invalid_id'], 401);
        }
    }
    /**
     * Create a post
     *
     * @param  Request  $request
     * @return Response
     */
    public function createPost(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'type'   => 'required|integer|between:0,2',
            'content' => 'required'
        ]);
        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        else {
            $article = new Post;
            $article->author = Auth::user()->name;
            $article->title = $request->input('title');
            $article->type = $request->input('type');
            $article->content = $request->input('content');
            $article->save();
            return response()->json(['success' => 'success'], 200);
        }
    }

}