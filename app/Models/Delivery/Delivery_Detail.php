<?php

namespace App\Models\Delivery;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use PDF;

class Delivery_Detail extends Model
{
    use HasFactory; 
    // LogsActivity
    
    protected $table ='delivery_details';
    //only the `deleted` event will get logged automatically
    // protected static $logAttributes = ['id','name'];
    
    protected $fillable = [
        'delivery_journey_id',
        'order_number',
        'suburb',
        'zone',
        'cash_recieved',
        'change',
        'journey',
        'departure',
        'estimated_duration_arrival',
        'estimated_arrival',
        'attual_arrival',
        'estimated_duration_return',
        'estimated_return',
        'actual_return',
        'fuel_cost',
        'approve',

    ];

    public function delivery_journey()
    {
        return $this->belongsTo(Delivery_Journey::class);
    }
    public function good_materials()
    {
        return $this->hasMany(Good_material::class);
    }
}
