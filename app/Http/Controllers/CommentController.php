<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use denis660\Centrifugo\Centrifugo;
use Illuminate\Http\Request;

class CommentController extends Controller
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
        return Comment::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $blacklist = Post::query()
            ->join('blacklists', 'posts.user_id', '=', 'blacklists.user_id')
            ->where('blacklists.blocked_user_id', auth()->user()->id)
            ->get();
        if (count($blacklist)) {
            return response()->json(['error' => "Cant add the comment, you are in blacklist"], 400);

        } else {
            $request['user_id'] = auth()->user()->id;
            $comment = Comment::create($request->all());
            $allComment = Comment::all();
            $this->centrifugo->publish('comment', ["comment" => $allComment]);
            return $comment;
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
        $comment = Comment::find($id);
        if ($comment) {
            return $comment;
        } else {
            return response()->json(['error' => "Comment not found"], 404);
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
        $comment = Comment::find($id);
        if ($comment) {
            $comment->update($request->all());
            return $comment;
        } else {
            return response()->json(['error' => "Comment not found"], 404);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $comment = Comment::find($id);
        if ($comment) {
            if (auth()->user()->id == $comment->user_id) {
                $comment = Comment::destroy($id);
                $allComment = Comment::all();
                $this->centrifugo->publish('comment', ["comment" => $allComment]);
                return $comment;
            } else {
                return response()->json(['error' => "You cant delete someone else's comment"], 400);
            }
        } else {
            return response()->json(['error' => "Comment not found"], 404);
        }

    }
}
