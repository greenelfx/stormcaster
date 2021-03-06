<?php

namespace App\Http\Controllers\API;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
     * Update a post
     *
     * @param  Request  $request
     * @param  Post  passed in post
     * @return Response
     */
    public function editPost(Request $request, Post $post) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'type'   => 'required|integer|between:0,2',
            'content' => 'required'
        ]);
        if ($validator->fails()) {
            return ['message' => 'validation', 'errors' => $validator->errors()->all()];
        }
        $post->title = $request->title;
        $post->type = $request->type;
        $post->content = $request->content;
        $post->save();
        return response()->json(['message' => 'success']);
    }

    /**
     * Delete a post
     *
     * @param  Request  $request
     * @param  Post  passed in post
     * @return Response
     */
    public function deletePost(Request $request, Post $post) {
        $post->delete();
        return response()->json(['message' => 'success']);
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
            return ['message' => 'validation', 'errors' => $validator->errors()->all()];
        }
        $post = new Post;
        $post->author = Auth::user()->name;
        $post->title = $request->title;
        $post->type = $request->type;
        $post->content = $request->content;
        $post->save();
        return response()->json(['message' => 'success'], 200);
    }
}