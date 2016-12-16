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
            'password' => 'required|min:6|max:12',
            'verify'   => 'required|same:password',
            'email'    => 'required|email|unique:accounts',
        ]);
        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        else {
            $user = new User;
            $user->name = $request->name;
            $user->password = sha1($request->password);
            $user->site_password = Hash::make($request->password);
            $user->email = $request->email;
            $user->birthday = "1990-01-01";
            $user->save();
            $token = JWTAuth::fromUser($user,['exp' => strtotime('+1 year'), 'type' => $user->webadmin, 'user_id' => $user->id]);
            return compact('token');
        }
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
            return $validator->errors()->all();
        }
        else {
            $loginfield = $request->input('loginfield');
            $field = filter_var($loginfield, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
            $user = User::where($field, '=', $loginfield)->where('password', '=', sha1($request->input('password')));
            if($user->count() == 1) {
                $user = $user->first();
                $token = JWTAuth::fromUser($user, ['type'=>$user->webadmin,'user_id'=> $user->id]);
                return response()->json(compact('token'));
            }
            return response()->json(['error' => 'invalid_credentials'], 401);
        }
    }
}