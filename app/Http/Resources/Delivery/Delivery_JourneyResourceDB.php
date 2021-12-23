<?php

namespace App\Http\Resources\Delivery;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class Delivery_JourneyResourceDB extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'date' => $this->date,
            'driver' => $this->driver,
            'route'=>$this->getRoute(),
            'departure'=>$this->getDeparture(),
            'duration'=>$this->getEstimatedDuration(),
            'est_return'=>$this->getEstimatedReturn(),
            // Carbon($new_delivery_detail->departure->format('Y-m-d H:i:s.u'),  $new_delivery_detail->departure->getTimezone());
            'actual_return' => $this->actual_return,
            'cust_pay'=>$this->getCashPaidByCustomers(),
            'change'=>$this->getChange(),
            'fuel_payment'=>$this->getFuelPayment(),
            // 'status' => $this->status,   
            'approve' => $this->approve,   
            
    
        ];
    }
    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }
}
