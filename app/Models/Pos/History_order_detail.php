<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Pos\Menu_Item;
use App\Models\Pos\History_Order_Head;

class History_Order_Detail extends Model
{
    protected $table = 'history_order_detail';
    protected $primaryKey = "order_detail_id";
    public $timestamps = false;
    protected $connection ='mysql2';
    use HasFactory;
    protected $fillable = [
        'order_detail_id',
        'menu_item_id',
        'menu_item_name',
        // 'product_price',
        // 'actual_price',
        'is_return_item',
        // 'order_employee_id',
        'order_employee_name',
        // 'pos_device_id',
        // 'pos_name',
        // 'order_time',
        'return_time',
        // 'return_reason',
        'unit',
        'condiment_belong_item',
        'quantity',
        'is_made',
        // 'is_send',
        // 'auth_id',
        // 'auth_name',
        'description',
        // 'discount_price',
    ];

    public function history_order_head()
    {
        return $this->belongsTo(History_Order_Head::class,'order_head_id');
    }

 
    public function menu_item()
    {
        return $this->belongsTo(Menu_Item::class,'menu_item_id','item_id');
    }

    function condiments()
    {
        return $this->hasMany(History_Order_Detail::class, 'condiment_belong_item', 'order_detail_id');
    }

    function getParentCondiments()
    {
        return $this->belongsTo(History_Order_Detail::class, 'condiment_belong_item');
    }

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


}
