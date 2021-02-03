<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use Illuminate\Http\Request;

class FollowerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Follower::all();
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
            $followerId = $request->follower_id;
            if ($user->id == $followerId) {
                return "You can't subscribe to yourself";
            } else if ($user->id != $userId) {
                return "You can't subscribe instead someone else's";
            } else {
                return Follower::create($request->all());
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
            return Follower::find($id);
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
            $follower = Follower::find($id);
            if ($user->id == $follower->user_id) {
                $follower->update($request->all());
                return $follower;
            } else {
                return "You cant update someone else's follow";
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
            $follower = Follower::find($id);
            if ($user->id == $follower->user_id) {
                $follower = Follower::destroy($id);
                return $follower;
            } else {
                return "You cant delete someone else's follow";
            }
        }

    }
}
