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
    /**
     * Disconnect a game account
     *
     * @param  Request  $request
     * @return Response
    */
    public function disconnectAccount(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $user->loggedin = 0;
        return ['status' => 'success', 'message' => 'Successfully disconnected account'];
    }

    /**
     * Modify the password of an account
     *
     * @param  Request  $request
     * @return Response
    */
    public function updateAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'new_password'   => 'required|min:6',
            'new_verify_password' => 'required|same:new_password',
        ]);
        if ($validator->fails()) {
            return ['message' => 'validation', 'errors' => $validator->errors()->all()];
        }
        if (!Hash::check($request->password, Auth::user()->site_password)) {
            return ['message' => 'invalid_info'];
        }
        $user = Auth::user();
        $user->password = sha1($request->new_password);
        $user->site_password = Hash::make($request->new_password);
        $user->save();
        return response()->json(['message' => 'success'], 200);
    }
}