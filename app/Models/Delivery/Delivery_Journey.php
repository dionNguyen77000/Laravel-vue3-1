<?php

namespace App\Models\Delivery;

use PDF;
use Illuminate\Database\Eloquent\Model;
use App\Models\Delivery\Delivery_Detail;
use App\Models\Delivery\Delivery_Journey;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery_Journey extends Model
{
    use HasFactory; 
    
    // protected static $logAttributes = ['id','name'];
    protected $table ='delivery_journeys';

    protected $fillable = [
        'date', 
        'driver',
        'actual_return',
        'approve',

    ];

    public function getRoute(){
        $the_delivery_details=  $this->delivery_details;
        $route = 'GLY';
        foreach ($the_delivery_details as $the_delivery_detail) {
           
            $route = $route . '->' .$the_delivery_detail->zone;
        }
        return  $route;
    }
    public function getDeparture(){
        $the_first_delivery_detail=  $this->delivery_details->first();
        return $the_first_delivery_detail->departure;
    }
    public function getEstimatedDuration(){
        $the_delivery_details=  $this->delivery_details;
        $duration = 0;
        foreach ($the_delivery_details as $the_delivery_detail) {
            $duration = $duration + $the_delivery_detail->estimated_duration_arrival + $the_delivery_detail->estimated_duration_return;
        }
        return   $duration ;
    }
    public function getEstimatedReturn(){
        $the_first_delivery_detail=  $this->delivery_details->last();
        return $the_first_delivery_detail->estimated_return;
    }
    public function getFuelPayment(){
        $the_delivery_details=  $this->delivery_details;
        $fuel_payment = 0.00;
        foreach ($the_delivery_details as $the_delivery_detail) {
            $fuel_payment = $fuel_payment + $the_delivery_detail->fuel_cost;
        }
        return   $fuel_payment ;
    }
    public function getChange(){
        $the_delivery_details=  $this->delivery_details;
        $change = 0.00;
        foreach ($the_delivery_details as $the_delivery_detail) {
            $change = $change + $the_delivery_detail->change;
        }
        return   $change ;
    }
    public function getCashPaidByCustomers(){
        $the_delivery_details=  $this->delivery_details;
        $cash = 0.00;
        foreach ($the_delivery_details as $the_delivery_detail) {
            $cash = $cash + $the_delivery_detail->cash_received;
        }
        return   $cash ;
    }

    public function delivery_details()
    {
        return $this->hasMany(Delivery_Detail::class,'delivery_journey_id');
    }

   
}
