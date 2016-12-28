<?php

namespace App\Http\Controllers\API;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;
use Auth;
use JWTAuth;
use Hash;

class AuthController extends Controller
{

    /**
     * Create a user
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:accounts|max:127',
            'password' => 'required|min:6',
            'verify_password'   => 'required|same:password',
            'email'    => 'required|email|unique:accounts',
        ]);
        if ($validator->fails()) {
            return ['message' => 'validation', 'errors' => $validator->errors()];
        }
        $user = new User;
        $user->name = $request->name;
        $user->password = sha1($request->password);
        $user->site_password = Hash::make($request->password);
        $user->email = $request->email;
        $user->birthday = "1990-01-01";
        $user->save();
        $token = JWTAuth::fromUser($user,['exp' => strtotime('+1 year'), 'type' => $user->webadmin, 'user_id' => $user->id, 'user_name' => $user->name]);
        return ['message' => 'success', 'token' => $token];
    }
    
    /**
     * Authenticate a user
     *
     * @param  Request  $request
     * @return Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('loginfield', 'password');
        $validator = Validator::make($credentials, [
            'loginfield' => 'required|max:127',
            'password'   => 'required',
        ]);
        if ($validator->fails()) {
            return ['message' => 'validation', 'errors' => $validator->errors()];
        }
        $loginfield = $request->loginfield;
        $field = filter_var($loginfield, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        if (Auth::attempt([$field => $loginfield, 'password' => $request->password])) {
            $token = JWTAuth::fromUser(Auth::user(), ['type' => Auth::user()->webadmin,'user_id' => Auth::user()->id, 'user_name' => Auth::user()->name]);
            return ['message' => 'success', 'token' => $token];
        }
        return response()->json(['message' => 'invalid_credentials'], 401);
    }
}