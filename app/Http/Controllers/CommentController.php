<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use denis660\Centrifugo\Centrifugo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $userId = $request->user_id;
        if ($user->id == $userId) {
            $blacklist = DB::table('users')
                ->select('blacklists.blocked_user_id as  blocked user')
                ->join('blacklists', 'users.id', '=', 'blacklists.user_id')
                ->where('blacklists.blocked_user_id', $userId)
                ->get();

            if (count($blacklist)) {
                return response()->json(['error' => "Cant add the comment, you are in blacklist"], 400);

            } else {
                $comment = Comment::create($request->all());
                $allComment = Comment::all();
                $this->centrifugo->publish('comment', ["comment" => $allComment]);
                return $comment;
            }
        } else {
            return response()->json(['error' => "You can't add someone else's comment"], 400);
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
            if ($user->id == $comment->user_id) {
                $comment->update($request->all());
                return $comment;
            } else {
                return response()->json(['error' => "You cant update someone else's comment"], 400);
            }
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
            if ($user->id == $comment->user_id) {
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
