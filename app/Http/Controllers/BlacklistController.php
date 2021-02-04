<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
use Illuminate\Http\Request;

class BlacklistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return Blacklist::all();
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
        $blockeduserId = $request->blocked_user_id;
        if ($user->id == $blockeduserId) {
            return response()->json(['error' => "You can't add yourself to blacklist"], 400);
        } else if ($user->id != $userId) {
            return response()->json(['error' => "You can't add to blacklist instead someone else's"], 400);
        } else {
            return Blacklist::create($request->all());
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
        $blacklist = Blacklist::find($id);
        if ($blacklist) {
            return $blacklist;
        } else {
            return response()->json(['error' => "Blacklist not found"], 404);
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
        $blacklist = Blacklist::find($id);
        if ($blacklist) {
            if ($user->id == $blacklist->user_id) {
                $blacklist->update($request->all());
                return $blacklist;
            } else {
                return response()->json(['error' => "You cant update someone else's blacklist"], 400);
            }
        } else {
            return response()->json(['error' => "Blacklist not found"], 404);
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
        $blacklist = Blacklist::find($id);
        if ($blacklist) {
            if ($user->id == $blacklist->user_id) {
                $blacklist = Blacklist::destroy($id);
                return $blacklist;
            } else {
                return response()->json(['error' => "You cant delete someone else's blacklist"], 400);
            }
        } else {
            return response()->json(['error' => "Blacklist not found"], 404);
        }

    }
}
