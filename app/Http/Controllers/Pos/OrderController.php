<?php

namespace App\Http\Controllers\Pos;

use App\Models\Pos\Order_Head;
use App\Models\Pos\History_Order_Head;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Resources\Pos\OrderResource;
use App\Models\Pos\Order_Detail;
use App\Models\Pos\History_Order_Detail;

class OrderController extends Controller
{

    protected $orderInfoIds = [
        1239,1240,1241,1242,1243,1244,1245,1248,
        1272,1273,1274,1275,1276,1277,1278,1279,1280,
        6004,6111,6210,6211,6212,

    ];

    public function __construct()
    {
        // $this->middleware(['auth'])->only(['store','destroy']);
    }

    public function getRetrievedColumns()
    {
        return [
        'order_head_id',
        'check_number',
        // 'rvc_center_id',
        // 'rvc_center_name',
        // 'table_id',
        // 'table_name',
        'check_id',
        'open_employee_id',
        'open_employee_name',
        // 'customer_num',
        // 'customer_id',
        'customer_name',
        // 'pos_device_id',
        // 'pos_name',
        'order_start_time',
        // 'order_end_time',
        // 'should_amount',
        // 'return_amount',
        // 'discount_amount',
        // 'actual_amount',
        // 'print_count',
        'status',
        // 'eat_type',
        'check_name',
        // 'original_amount',
        // 'service_amount',
        'edit_time',
        // 'party_id',
        'edit_employee_name',
        'remark',
        'is_make',
        'delivery_info',
        // 'kds_show',
        // 'kds_time',
        // 'tax_amount',
        // 'raw_talbe',
        ];
    }


    public function index(Request $request)
    {
    //   return $this->builder->get();
      return response()->json([
        'data' => [
          'orders' => $this->getOrders($request),
        ]

      ]);
    }

    public function getOrders (Request $request) {
        // return 'hello';
        $prep = ['0'];
        if(isset($request->preparation)){
            if($request->preparation == 1){
                $prep = ["1"];
            }elseif ($request->preparation == 2){
                $prep = ["0","1"];
            }
        }

        $startTime =Carbon::today('Australia/Brisbane');
        $endTime = Carbon::tomorrow('Australia/Brisbane');

        if (isset($request->selected_time) && $request->selected_time != 'All')  {

           if($request->selected_time=='Yesterday'){
                $startTime =Carbon::yesterday('Australia/Brisbane');
                $endTime = Carbon::yesterday('Australia/Brisbane');               
            }
             elseif($request->selected_time=='Two_days_Ago'){   
                $startTime =Carbon::yesterday('Australia/Brisbane')->subDays(1);
                $endTime = Carbon::yesterday('Australia/Brisbane')->subDays(1);  
            }            
             elseif($request->selected_time=='Current_Week'){   
                $startTime =Carbon::now('Australia/Brisbane')->startOfWeek();
                $endTime = Carbon::today('Australia/Brisbane');            
            }            
        }
        // dd($request->selected_time);
        // dd(Carbon::now()->isoFormat('dddd D'));
         
        // dd(Order_Detail::find(2)->created_at->isoFormat('dddd D')); //This return Carbon- third party - can read document to find out how to use it
        // $posts = Order_Detail::orderBy('created_at','desc') or latest()
        $orders =Order_Head::orderBy('order_head_id', 'desc')

        ->where('order_head_id','>','0')
        // ->whereDate('order_start_time',">=",Carbon::now('Australia/Brisbane')->startOfWeek())
        // ->whereDate('order_start_time',Carbon::today('Australia/Brisbane'))
        ->whereDate('order_start_time',">=", $startTime)
        ->whereDate('order_start_time',"<=", $endTime)
        // ->whereDate('order_start_time',Carbon::yesterday('Australia/Brisbane'))
        // ->whereDate('order_start_time','>=',Carbon::yesterday('Australia/Brisbane'))
        // ->whereDate('order_start_time','>=',Carbon::yesterday('Australia/Brisbane')->subDays(1))
        // ->whereDate('order_start_time',">=",Carbon::now('Australia/Brisbane')->startOfWeek()->subWeek())
        // ->whereDate('order_start_time',">=",Carbon::now('Australia/Brisbane')->subDays(Carbon::now('Australia/Brisbane')->dayOfWeek + 1))
        ->whereIn('is_make',$prep)
        ->get($this->getRetrievedColumns())      
        ->load(['order_details','order_details.menu_item','order_details.condiments']);
        // dd($orders);
        
        
        
        $orders_history = History_Order_Head::orderBy('order_head_id', 'desc')
        ->where('order_head_id','>','0')
        // ->whereDate('order_start_time',Carbon::today('Australia/Brisbane'))
        ->whereDate('order_start_time',">=", $startTime)
        ->whereDate('order_start_time',"<=", $endTime)
        // ->whereDate('order_start_time',Carbon::yesterday('Australia/Brisbane'))
        // ->whereDate('order_start_time','>=',Carbon::yesterday('Australia/Brisbane'))
        // ->whereDate('order_start_time','>=',Carbon::yesterday('Australia/Brisbane')->subDays(1))
        // ->whereDate('order_start_time',">=",Carbon::now('Australia/Brisbane')->startOfWeek())
        // ->whereDate('order_start_time',">=", Carbon::now('Australia/Brisbane')->startOfWeek()->subWeek())
        ->whereIn('is_make',$prep)
        ->get($this->getRetrievedColumns())  
        ->load(['history_order_details','history_order_details.menu_item','history_order_details.condiments']);
        
        // dd($orders);
        // dd($orders_history);
        
        // dd($orders);
        
        $orders = $orders ->concat($orders_history);
        // dd($orders);

        // dd($orders);

        $order_resource = OrderResource::collection(
            $orders
        );
        // $order_history_resource = OrderResource::collection(
        //     $orders_history
        // );

        // $all_orders = $order_resource ->concat($order_history_resource);


      
        // return $all_orders;
        return $order_resource;
    
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

    //     // Order_Detail::create([
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

        // Order_Detail::create([
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
        
        // $post = Order_Detail::find($id);
        // $post->update($request->only('body'));
        // return $post;
    }

    
    public function completeOrder($order_head_id){
        $theOrder = Order_Head::where('order_head_id',$order_head_id)->first();

        if(!$theOrder){
            
            $theOrder = History_Order_Head::where('order_head_id',$order_head_id)->first();
        }

        if($theOrder->is_make == "0") {
            $theOrder->is_make = "1";
            $theOrder->save();
        }
        elseif($theOrder->is_make == "1") {
            $theOrder->is_make = "0";
            $theOrder->save();
        }
        return $theOrder->is_make;
    }
    public function completeOrders($order_head_ids){
        // dd($order_head_ids);
        $arrayIds = explode(',',$order_head_ids);

        foreach ($arrayIds as $order_head_id) {
            # code...
            $theOrder = Order_Head::where('order_head_id',$order_head_id)->first();
            
            if(!$theOrder){              
                $theOrder = History_Order_Head::where('order_head_id',$order_head_id)->first();
            }
            $theOrder->save();

            if($theOrder->is_make == "0") {
                $theOrder->is_make = "1";
                $theOrder->save();
            }
            elseif($theOrder->is_make == "1") {
                $theOrder->is_make = "0";
                $theOrder->save();
            }
        }

        return 'complete Orders';
    }
    public function completeTheItem($order_detail_id){
        $theItem = Order_Detail::where('order_detail_id',$order_detail_id)->first();

        if(!$theItem){
            
            $theItem = History_Order_Detail::where('order_detail_id',$order_detail_id)->first();
        }

        if($theItem->is_make ==  null || $theItem->is_make ==  "0") {
            $theItem->is_make = "1";
            $theItem->save();
        }
        elseif($theItem->is_make == "1") {
            $theItem->is_make = "0";
            $theItem->save();
        }

        return $theItem->is_make;
    }
    

}
