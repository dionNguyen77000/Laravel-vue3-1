<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class UserPostController extends Controller
{
    public function index(User $user)
    {
        // $posts = Post::latest();
        // $posts = $user->posts()->get()->sortByDesc("id");
        $posts = $user->posts()->with(['user','likes'])->orderBy("id",'desc')->paginate(15);

        // $posts = $user->posts()->get()->sortByDesc("id");
        // dd($posts);

        return view('users.posts.index',[
            'user' => $user, 
            'posts' => $posts
        ]);
    }
}
