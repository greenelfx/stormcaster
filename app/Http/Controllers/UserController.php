<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Validator;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function __construct() 
    {
       // Apply the jwt.auth middleware to all methods in this controller
       // except for the authenticate method. We don't want to prevent
       // the user from retrieving their token if they don't already have it
       $this->middleware('jwt.auth', ['except' => ['createUser', 'authenticate']]);
   }
    /**
     * Create a user
     *
     * @param  Request  $request
     * @return Response
     */
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:accounts|max:127',
            'password' => 'required|min:6|max:12',
            'verify'   => 'required|same:password',
            'email'	   => 'required|email|unique:accounts',
        ]);

        if ($validator->fails()) {
        	return $validator->errors()->all();
        }
        else {
        	$user = new \App\User;
        	$user->name = $request['name'];
        	$user->password = sha1($request['password']);
        	$user->email = $request['email'];
        	$user->save();

        	return array('success', 'true');
        }
    }
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('loginfield', 'password');
        $validator = Validator::make($credentials, [
            'loginfield' => 'required|max:127',
            'password'   => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        else {
            $loginfield = $request->input('loginfield');
            $field = filter_var($loginfield, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
            $user = \App\User::where($field, '=', $loginfield)->where('password', '=', sha1($request->input('password')));
            if($user->count() == 1) {
                $user = $user->first();
                $token = JWTAuth::fromUser($user, ['type'=>$user->webadmin,'username'=>$user->name]);
                return response()->json(compact('token'));
            }
            return response()->json(['error' => 'invalid_credentials'], 401);
        }
    }
    public function disconnectAccount(Request $request) {
        $user = User::find(Auth::user()->id);
        $user->loggedin = 0;
        return "true";
    }
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
}