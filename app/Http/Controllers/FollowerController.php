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
        $userId = $request->user_id;
        $followerId = $request->follower_id;
        if ($user->id == $followerId) {
            return response()->json(['error' => "You can't subscribe to yourself"], 400);
        } else if ($user->id != $userId) {
            return response()->json(['error' => "You can't subscribe instead someone else's"], 400);
        } else {
            return Follower::create($request->all());
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
        $follower = Follower::find($id);
        if ($follower) {
            return $follower;
        } else {
            return response()->json(['error' => "Follower not found"], 404);
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
        $follower = Follower::find($id);
        if($follower){
            if ($user->id == $follower->user_id) {
                $follower->update($request->all());
                return $follower;
            } else {
                return response()->json(['error' => "You cant update someone else's follow"], 400);
            }
        }
       else{
        return response()->json(['error' => "Follower not found"], 404);
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
        $follower = Follower::find($id);
        if($follower){
            if ($user->id == $follower->user_id) {
                $follower = Follower::destroy($id);
                return $follower;
            } else {
                return response()->json(['error' => "You cant delete someone else's follow"], 400);
            }
        }
       else{
        return response()->json(['error' => "Follower not found"], 404);
       }

    }
}
