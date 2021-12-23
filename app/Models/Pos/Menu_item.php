<?php

namespace App\Models\Pos;

use App\Models\Pos\Family_Group;
use App\Models\Pos\Order_Detail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu_Item extends Model
{
    protected $table = 'menu_item';
    public $timestamps = false;
    protected $primaryKey = "item_id";
    protected $connection ='mysql2';
    use HasFactory;
    protected $fillable = [
        'item_id',
        'item_name1',
        // 'class_id',
        // 'print_class',
        // 'item_type',
        'allow_condiment',
        'required_condiment',
        // 'check_availability',
        // 'no_access_mgr',
        'major_group',
        'family_group',
        'price_1',
        // 'cost_1',
        'unit_1',
        'unit_2',
    ];

    // protected $appends = ['howLong'];

    // public function getHowLongAttribute()
    // {
    //     return $this->created_at->diffForHumans();
    // }

    // public function likedBy(User $user){
    //     return $this->likes->contains('user_id', $user->id);  //contains: colleciton method
    // }

    // public function ownedBy(User $user)
    // {
    //     return (int)$user->id === (int)$this->user_id;
    // }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function likes()
    // {
    //     return $this->hasMany(Like::class);
    // }


    // public function getMajorGroup(){
    //     $order_head = Order_Head::where('order_head_id',$this->order_head_id)->first();
       
    //     return $order_head;

    //     // return   $duration ;
    // }
    // public function getSecondaryGroup(){
    //     $order_head = Order_Head::where('order_head_id',$this->order_head_id)->first();
       
    //     return $order_head;

    //     // return   $duration ;
    // }
    // public function getCustomerName(){
    //     $order_head = Order_Head::where('order_head_id',$this->order_head_id)->first();
       
    //     return $order_head->check_number;
       
    // }

    public function order_details()
    {
        return $this->hasMany(Order_Detail::class,'menu_item_id','item_id');
    }

    public function history_order_details()
    {
        return $this->hasMany(History_Order_Detail::class,'menu_item_id','item_id');
    }

    public function fam_group()
    {
        return $this->belongsTo(Family_Group::class,'family_group','family_group_id');
    }
}
