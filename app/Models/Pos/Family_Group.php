<?php

namespace App\Models\Pos;

use App\Models\Pos\Menu_Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Family_Group extends Model
{
    protected $table = 'family_group';
    public $timestamps = false;
    protected $primaryKey = "family_group_id";
    protected $connection ='mysql2';
    use HasFactory;
    protected $fillable = [
        'family_group_id',
        'family_group_name',
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
    public function menu_items()
    {
        return $this->hasMany(Menu_Item::class,'family_group','family_group_id');
    }
}
