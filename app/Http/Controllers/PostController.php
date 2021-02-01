<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Policies\PostPolicy;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth'])->only(['store','destroy']);
    }

    public function index()
    {
        // dd(Carbon::now()->isoFormat('dddd D'));
         
        // dd(Post::find(2)->created_at->isoFormat('dddd D')); //This return Carbon- third party - can read document to find out how to use it
        // $posts = Post::orderBy('created_at','desc') or latest()
        $posts = Post::latest()->with(['user','likes'])->paginate(15) ; //all Post::where or Post::find(1) - return collection
        // $posts = Post::all()-> sortByDesc("id")->paginate(20);
        // $posts = Post::paginate(2);
        // $posts= Post::all();
        // dd($posts);

        // return view('posts.index');

        return view('posts.index',[
         'posts' => $posts
         ]);
    }

    public function show(Post $post){
        return view('posts.show',[
            'post' => $post
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'body' => 'required'
        ]);

        // Post::create([
        //     'user_id' => auth()->id(),
        //     'body' => $request->body
        // ]);

        // $request->user()->posts()->create([
        //     'body' => $request->body
        // ]);

        $request->user()->posts()->create($request->only('body'));

        return back();
    }

    public function destroy(Post $post)
    {
        // to check if the correct user to delete 
        // if (!$post->ownedBy(auth()->user())){
        //     dd('no');
        // }

        //using policy to check if the correct user delete
        $this->authorize('delete',$post);

        $post->delete();
        return back();
    }

}
