<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Resources\Json\JsonResource;

class Orders_To_SupplierResourceDB extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // public function toArray($request)
    // {
    //     return [
    //         'id'=>$this->id,
    //         'user_id'=>$this->user_id,
    //         'invoices_from_supplier_id'=>$this->invoices_from_supplier_id,
    //         'ordered_date'=>$this->ordered_date,
    //         'estimated_price'=>$this->estimated_price         
    //     ];
    // }

    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
