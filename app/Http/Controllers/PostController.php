<?php

namespace App\Http\Controllers;

use App\Models\Post;
use denis660\Centrifugo\Centrifugo;
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
        $user = auth()->user();
        if ($user) {
            $userId = $request->user_id;
            if ($user->id == $userId) {
                $post = Post::create($request->all());
                $allPosts = Post::all();
                $this->centrifugo->publish('posts', ["posts" => $allPosts]);
                return $post;
            }
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
        $user = auth()->user();
        if ($user) {
            return Post::find($id);
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
        if ($user) {
            $post = Post::find($id);
            if ($user->id == $post->user_id) {
                $post->update($request->all());
                return $post;
            } else {
                return "You can't update someone else's post";
            }
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
        if ($user) {
            $post = Post::find($id);
            if ($user->id == $post->user_id) {
                $post = Post::destroy($id);
                $allPosts = Post::all();
                $this->centrifugo->publish('posts', ["posts" => $allPosts]);
                return $post;
            } else {
                return 'You cant delete someone else`s post';
            }
        }

    }
}
