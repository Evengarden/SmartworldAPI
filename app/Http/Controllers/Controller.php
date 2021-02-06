<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Post;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function UpdateUserInfoRedis(int $id){
        $user = User::find($id);

        

        $followers =DB::table('users')
        ->select('name')
        ->leftJoin('followers','followers.user_id','=','users.id')
        ->where('followers.user_id','=',$id)
        ->get();

        
        $following = DB::table('users')
        ->select('name')
        ->leftJoin('followers','followers.user_id','=','users.id')
        ->where('followers.follower_id','=',$id)
        ->get();
        $userInfo = [
            'User info' => $user,
            'Followers info' =>$followers,
            'Following info' => $following
        ];

        Redis::set('user_info/'.$id, json_encode($userInfo));
    }

    public function UpdateUserPostRedis(int $id){
        $post = Post::all()->where('user_id',$id);
        
        Redis::set('user_post/'.$id,$post);
    }

    public function DeleteUserPostRedis(int $id){
        Redis::del('user_post/'.$id);
    }
}
