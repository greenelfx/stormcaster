<?php

namespace App\Http\Controllers\API;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;
use App\Models\News;
use Auth;
use JWTAuth;
use Hash;

class AdminController extends Controller
{
    public function __construct() 
    {
       $this->middleware('jwt.auth');
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
                $article = News::findOrFail($request->input('id'));
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
                $article = new News;
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
}