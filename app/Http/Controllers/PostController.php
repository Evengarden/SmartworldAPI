<?php

namespace App\Http\Controllers;

use App\Models\Post;
use denis660\Centrifugo\Centrifugo;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private $centrifugo;

    /**
     * Class __construct
     *
     * @param Centrifugo $centrifugo
     */
    public function __construct(Centrifugo $centrifugo)
    {
        $this->centrifugo = $centrifugo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Post::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = $request->user_id;
        if (auth()->user()->id == $userId) {
            $post = Post::create($request->all());
            $this->UpdateUserPostRedis($userId);
            $allPosts = Post::all();
            $this->centrifugo->publish('posts', ["posts" => $allPosts]);
            return $post;
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(is_null($id)){
            $post = Redis::get('user_post/'.auth()->user()->id);
        }
        else{
            $post = Post::Find($id);
        }
        if ($post) {
            return $post;
        } else {
            return response()->json(['error' => "Post not found"], 404);
        }
           

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $post = Post::find($id);
        if ($post) {
            if ($user->id == $post->user_id) {
                $post->update($request->all());
                $this->UpdateUserPostRedis($user->id);
                return $post;
            } else {
                return response()->json(['error' => "You can't update someone else's post"], 400);
            }
        } else {
            return response()->json(['error' => "Post not found"], 404);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $post = Post::find($id);
        if ($post) {
            if ($user->id == $post->user_id) {
                $post = Post::destroy($id);
                $allPosts = Post::all();
                $this->DeleteUserPostRedis($user->id);
                $this->centrifugo->publish('posts', ["posts" => $allPosts]);
                return $post;
            } else {
                return response()->json(['error' => "You cant delete someone else`s post"], 400);
            }
        } else {
            return response()->json(['error' => "Post not found"], 404);
        }

    }
}
