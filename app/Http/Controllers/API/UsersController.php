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
            return response()->json(['error' => 'invalid_name'], 401);
        }
    }
}