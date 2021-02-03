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
        $user = auth()->user();
        if ($user) {
            $userId = $request->user_id;
            $blockeduserId = $request->blocked_user_id;
            if ($user->id == $blockeduserId) {
                return "You can't add yourself to blacklist";
            } else if ($user->id != $userId) {
                return "You can't add to blacklist instead someone else's";
            } else {
                return Blacklist::create($request->all());
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
            return Blacklist::find($id);

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
            $blacklist = Blacklist::find($id);
            if ($user->id == $blacklist->user_id) {
                $blacklist->update($request->all());
                return $blacklist;
            } else {
                return "You cant update someone else's blacklist";
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
            $blacklist = Blacklist::find($id);
            if ($user->id == $blacklist->user_id) {
                $blacklist = Blacklist::destroy($id);
                return $blacklist;
            } else {
                return "You cant delete someone else's blacklist";
            }
        }

    }
}
