<?php
declare (strict_types = 1);
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::forceCreate([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'api_token' => Str::random(80),
        ]);
        if ($user) {
            $this->UpdateUserInfoRedis($user->id);
            return $user;
        } else {
            return response()->json(['error' => "Bad request"], 400);
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
        $user = Redis::get('user_info/' . $id);
        if ($user) {
            return $user;
        } else {
            return response()->json(['error' => "User not found"], 404);
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
        $userId = $id;
        if (auth()->user()->id == $userId) {
            $user = User::find($id);
            $user->update($request->all());
            $this->UpdateUserInfoRedis(auth()->user()->id);
            return $user;
        } else {
            return response()->json(['error' => "You cant update someone else's profile"], 400);
        }

    }

    public function Authorization(Request $request)
    {
        $email = $request->email;
        $password = DB::table('users')->where('email', $email)->first()->password;
        $auth = auth()->attempt(['email' => $email, 'password' => $request->password]);
        if ($auth) {
            return $auth;
        } else {
            return response()->json(['error' => 'Bad request'], 400);
        }

    }

    public function news(Request $request)
    {
        $news = DB::select('select * from posts
            Join followers on
            followers.follower_id = posts.user_id
            where posts.user_id = followers.follower_id and
            followers.user_id = ' . auth()->user()->id . ' LIMIT 50');
        return $news;

    }

    public function getPosts(Request $request)
    {
        $userId = $request->user_id;
        $posts = DB::table('posts')->where('user_id', $userId)->get();
        if ($posts) {
            return $posts;
        } else {
            return response()->json(['error' => "Posts not found"], 404);
        }

    }

    public function getProfileInfo(Request $request)
    {
        $userId = $request->user_id;
        $followers = DB::table('users')
            ->select('followers.user_id as user', 'followers.follower_id as follower')
            ->join('followers', 'users.id', '=', 'followers.user_id')
            ->where('followers.user_id', $userId)
            ->orWhere('followers.follower_id', $userId)
            ->get();
        if ($user->id == $userId) {
            return $followers;
        } else {
            $blacklist = DB::table('users')
                ->select('blacklists.blocked_user_id as  blocked user')
                ->join('blacklists', 'users.id', '=', 'blacklists.user_id')
                ->where('blacklists.blocked_user_id', $userId)
                ->get();

            if (count($blacklist)) {
                return response()->json(['error' => "Cant show profile info, you are in blacklist"], 400);

            } else {
                return $followers;
            }
        }

    }
}
