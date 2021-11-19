<?php

namespace App\Models\Delivery;

use Illuminate\Database\Eloquent\Model;
// use App\Models\Delivery\Delivery_Detail;
// use App\Models\Delivery\Delivery_Travel_Time;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery_Travel_Time extends Model
{
    use HasFactory; 
    
    protected $table ='delivery_travel_times';

    protected $fillable = [
        'destination_one_id',
        'destination_two_id',
        'estimated_duration',
    ];

}