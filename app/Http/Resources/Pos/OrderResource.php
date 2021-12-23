<?php

namespace App\Http\Resources\Pos;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
// use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderResource extends JsonResource
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
                 
            'order_head_id'=>$this->order_head_id,
            'check_number'=>$this->check_number,
            // 'rvc_center_id'=>$this->rvc_center_id,
            // 'rvc_center_name'=>$this->rvc_center_name,
            // 'table_id'=>$this->table_id,
            // 'table_name'=>$this->table_name,
            // 'check_id'=>$this->check_id,
            // 'open_employee_id'=>$this->open_employee_id,
            'open_employee_name'=>$this->open_employee_name,
            // 'customer_num'=>$this->customer_num,
            // 'customer_id'=>$this->customer_id,
            'customer_name'=>$this->customer_name,
            // 'pos_device_id'=>$this->pos_device_id,
            // 'pos_name'=>$this->pos_name,
            'order_start_time'=>$this->order_start_time,
            // 'howlong'=>$this->order_start_time->diffForHumans(),
            'order_end_time'=>$this->order_end_time,
            // 'should_amount'=>$this->should_amount,
            // 'return_amount'=>$this->return_amount,
            // 'discount_amount'=>$this->discount_amount,
            // 'actual_amount'=>$this->actual_amount,
            // 'print_count'=>$this->print_count,
            'status'=>$this->status,
            // 'eat_type'=>$this->eat_type,
            'check_name'=>$this->check_name,
            // 'original_amount'=>$this->original_amount,
            // 'service_amount'=>$this->service_amount,
            // 'edit_time'=>$this->edit_time,
            // 'party_id'=>$this->party_id,
            // 'edit_employee_name'=>$this->edit_employee_name,
            'remark'=>$this->remark,
            'is_make'=>$this->is_make,
            'delivery_info'=>$this->delivery_info,
            // 'kds_show'=>$this->kds_show,
            // 'kds_time'=>$this->kds_time,
            // 'tax_amount'=>$this->tax_amount,
            // 'raw_talbe'=>$this->raw_talbe,
            'order_details'=>$this->getOrderDetails(),
            // 'waiting_time'=>$this->getWaitingTime(),  
            // 'pickup_time'=>$this->getHandleTime(),  
            'remaining_time'=>$this->getRemainingTime(),  
        ];
      
    }
    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }
}
