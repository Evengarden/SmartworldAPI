<?php

namespace App\Http\Controllers;

use App\Models\Post;
use denis660\Centrifugo\Centrifugo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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
        $request->validate([
            'theme' => ['required', 'string'],
            'text' => ['required', 'string'],
        ]);
        $request['user_id'] = auth()->user()->id;
        $post = Post::create($request->all());
        $this->updateUserPostRedis(auth()->user()->id);
        $allPosts = Post::latest()->take(10)->get();
        $this->centrifugo->publish('posts', ['posts' => $allPosts]);
        return $post;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (is_null($id)) {
            $post = Redis::get('user_post/' . auth()->user()->id);
        } else {
            $post = Post::find($id);
        }
        if ($post) {
            return $post;
        } else {
            return response()->json(['error' => 'Post not found'], 404);
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
        $request->validate([
            'theme' => ['required', 'string'],
            'text' => ['required', 'string'],
        ]);
        $post = Post::find($id);
        if ($post) {
            if (auth()->user()->id == $post->user_id) {
                $post->update($request->all());
                $this->updateUserPostRedis(auth()->user()->id);
                return $post;
            } else {
                return response()->json(['error' => 'You can`t update someone else`s post'], 400);
            }
        } else {
            return response()->json(['error' => 'Post not found'], 404);
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
        $post = Post::find($id);
        if ($post) {
            if (auth()->user()->id == $post->user_id) {
                $post = Post::destroy($id);
                $allPosts = Post::latest()->take(10)->get();
                $this->deleteUserPostRedis(auth()->user()->id);
                $this->centrifugo->publish('posts', ['posts' => $allPosts]);
                return response()->json(['message' => 'Post deleted']);
            } else {
                return response()->json(['error' => 'You cant delete someone else`s post'], 400);
            }
        } else {
            return response()->json(['error' => 'Post not found'], 404);
        }

    }
}
