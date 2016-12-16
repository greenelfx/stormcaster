<?php

namespace App\Http\Controllers\API;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;
use Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Hash;

class UsersController extends Controller
{
    public function __construct() 
    {
       // Apply the jwt.auth middleware to all methods in this controller
       // except for the authenticate method. We don't want to prevent
       // the user from retrieving their token if they don't already have it
       $this->middleware('jwt.auth', ['except' => ['submitVote']]);
   }

    /**
     * Disconnect a game account
     *
     * @param  Request  $request
     * @return Response
     */
    public function disconnectAccount(Request $request) {
        $user = User::find(Auth::user()->id);
        $user->loggedin = 0;
        return ['status' => 'success', 'message' => 'Successfully disconnected account'];
    }
    /**
     * Get ID and titles of all news articles
     *
     * @return Response
     */
    public function getEditableNews(Request $request) {
        $user = User::find(Auth::user()->id);
        if($user->webadmin == 1) {
            $news = \App\News::get(array('id', 'title', 'author'));
            return $news;
        }
        else {
            return response()->json(['error' => 'invalid_credentials'], 401);
        }
    }
    /**
     * Update a news article
     *
     * @param  Request  $request
     * @return Response
     */
    public function editNews(Request $request) {
        $user = User::find(Auth::user()->id);
        if($user->webadmin == 1) {
            try {
                $article = \App\News::findOrFail($request->input('id'));
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
        else {
           return response()->json(['error' => 'invalid_credentials'], 401);
        }
    }
    /**
     * Delete a news article
     *
     * @param  Request  $request
     * @return Response
     */
    public function deleteNews(Request $request) {
        $user = User::find(Auth::user()->id);
        if($user->webadmin == 1) {
            try {
                $article = \App\News::findOrFail($request->input('id'));
                $article->delete();
                return response()->json(['success' => 'success'], 200);
            }
            catch(ModelNotFoundException $e) {
                return response()->json(['error' => 'invalid_id'], 401);
            }
        }
        else {
           return response()->json(['error' => 'invalid_credentials'], 401);
        }
    }
    /**
     * Create a news article
     *
     * @param  Request  $request
     * @return Response
     */
    public function createNews(Request $request) {
        $user = User::find(Auth::user()->id);
        if($user->webadmin == 1) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'type'   => 'required|string',
                'content' => 'required'
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            else {
                $article = new \App\News;
                $article->author = Auth::user()->name;
                $article->date = time();
                $article->title = $request->input('title');
                $article->type = $request->input('type');
                $article->content = $request->input('content');
                $article->save();
                return response()->json(['success' => 'success'], 200);
            }           
        }
        else {
           return response()->json(['error' => 'invalid_credentials'], 401);
        }        
    }
    public function submitVote(Request $request) {
        $credentials = $request->only('name');
        $validator = Validator::make($credentials, [
            'name' => 'required|max:127',
        ]);
        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        else {
            $user = User::where('name', '=', $request->input('name'));
            if($user->count() == 1) {
                $user = $user->first();
                return response()->json(['success' => 'success'], 200);
            }
            else {
                return response()->json(['error' => 'invalid_name'], 401);
            }
        }
    }
}