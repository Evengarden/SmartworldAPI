<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function updateUserInfoRedis(int $userId)
    {
        $user = User::find($userId);

        $followers = User::query()
            ->select('name')
            ->leftJoin('followers', 'followers.user_id', '=', 'users.id')
            ->where('followers.user_id', '=', $userId)
            ->get();

        $following = User::query()
            ->select('name')
            ->leftJoin('followers', 'followers.user_id', '=', 'users.id')
            ->where('followers.follower_id', '=', $userId)
            ->get();
        $userInfo = [
            'User info' => $user,
            'Followers info' => $followers,
            'Following info' => $following,
        ];

        Redis::set('user_info/' . $userId, json_encode($userInfo));
    }

    public function updateUserPostRedis(int $userId)
    {
        $post = Post::all()->where('user_id', $userId);

        Redis::set('user_post/' . $userId, $post);
    }

    public function deleteUserPostRedis(int $userId)
    {
        Redis::del('user_post/' . $userId);
    }
}
