<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
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

        Redis::set('user_info/'.$id, $user);
    }

    public function UpdateUserPostRedis(int $id){
        $post = Post::all()->where('user_id',$id);
        
        Redis::set('user_post/'.$id,$post);
    }
}
