<?php

namespace App\Http\Controllers\Pos;

use App\Models\Pos\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Resources\Pos\OrderResource;

class OrderDetailController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth'])->only(['store','destroy']);
    }

    // public function index()
    // {
    //     // dd(Carbon::now()->isoFormat('dddd D'));
         
    //     // dd(Order::find(2)->created_at->isoFormat('dddd D')); //This return Carbon- third party - can read document to find out how to use it
    //     // $posts = Order::orderBy('created_at','desc') or latest()
    //     $posts = Order::latest()->with(['user','likes'])->paginate(15) ; //all Order::where or Order::find(1) - return collection
    //     // $posts = Order::all()-> sortByDesc("id")->paginate(20);
    //     // $posts = Order::paginate(2);
    //     // $posts= Order::all();
    //     // dd($posts);

    //     // return view('posts.index');

    //     return view('posts.index',[
    //      'posts' => $posts
    //      ]);
    // }

    // public function index_vue()
    // {

    //     // dd(Carbon::now()->isoFormat('dddd D'));
         
    //     // dd(Order::find(2)->created_at->isoFormat('dddd D')); //This return Carbon- third party - can read document to find out how to use it
    //     // $posts = Order::orderBy('created_at','desc') or latest()
    //     $posts = Order::latest()->with(['user','likes'])->paginate(5) ; //all Order::where or Order::find(1) - return collection
    //     // $posts = Order::all()-> sortByDesc("id")->paginate(20);
    //     // $posts = Order::paginate(2);
    //     // $posts= Order::all();
    //     // dd($posts);

    //     return  $posts;
    // }

    public function index () {
        // return 'hello';
        
        // dd(Carbon::now()->isoFormat('dddd D'));
         
        // dd(Order::find(2)->created_at->isoFormat('dddd D')); //This return Carbon- third party - can read document to find out how to use it
        // $posts = Order::orderBy('created_at','desc') or latest()
        $order =Order::orderBy('order_detail_id', 'asc')->get();  //all Order::where or Order::find(1) - return collection
        

        // dd($order);

        $pr = OrderResource::collection(
            $order
            // ->get($this->getDisplayableColumns())
        );
      
        // dd($pr);
        return $pr;
        // $posts = Order::latest()->with(['user','likes'])->get() ; //all Order::where or Order::find(1) - return collection
        // $posts = Order::all()-> sortByDesc("id")->paginate(20);
        // $posts = Order::paginate(2);
        // $posts= Order::all();
        // dd($posts);

        // return  $posts;
    }
   

    // public function show(Order $post){
    //     return view('posts.show',[
    //         'post' => $post
    //     ]);
    // }

    // public function store(Request $request)
    // {
    //     $this->validate($request, [
    //         'body' => 'required'
    //     ]);

    //     // Order::create([
    //     //     'user_id' => auth()->id(),
    //     //     'body' => $request->body
    //     // ]);

    //     // $request->user()->posts()->create([
    //     //     'body' => $request->body
    //     // ]);

    //     $request->user()->posts()->create($request->only('body'));

    //     return back();
    // }
    public function store(Request $request)
    {
        $this->validate($request, [
            // 'body' => 'required'
        ]);

        // Order::create([
        //     'user_id' => auth()->id(),
        //     'body' => $request->body
        // ]);

        // $request->user()->posts()->create([
        //     'body' => $request->body
        // ]);

        // $post = $request->user()->posts()->create($request->only('body'));

        // return $post->load(['user']);
      
    }

    // public function destroy(Order $post)
    // {
    //     // to check if the correct user to delete 
    //     // if (!$post->ownedBy(auth()->user())){
    //     //     dd('no');
    //     // }

    //     //using policy to check if the correct user delete
    //     $this->authorize('delete',$post);

    //     $post->delete();
    //     return back();
    // }
    public function destroy(Order $post)
    {

        // to check if the correct user to delete 
        // if (!$post->ownedBy(auth()->user())){
        //     dd('no');
        // }

        // //using policy to check if the correct user delete
        // $this->authorize('delete',$post);
        
        // $post->delete();
        // return $post;
    }

    public function update($id, Request $request)
    {
        // return($id);
        // return $request;
        // $this->validate($request, [
        //     'body' => 'required',
        // ]);
        
        // $post = Order::find($id);
        // $post->update($request->only('body'));
        // return $post;
    }

}
