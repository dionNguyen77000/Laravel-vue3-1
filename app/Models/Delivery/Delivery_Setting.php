<?php

namespace App\Models\Delivery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery_Setting extends Model
{
    use HasFactory; 
    
    protected $table ='delivery_settings';

    protected $fillable = [
        'suburb', 
        'zone',
        'post_code',
        'street/road',
        'delivery_fee',
        'fuel_cost',
    ];

    public function travel_time()
    {
        return $this->belongsToMany(Delivery_Setting::class, 'delivery_travel_times', 'destination_one_id', 'destination_two_id');
    }
}