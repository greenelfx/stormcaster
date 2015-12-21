<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{
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
                $token = JWTAuth::fromUser($user->first());
                return response()->json(compact('token'));
            }
            return response()->json(['error' => 'invalid_credentials'], 401);
        }
    }
}