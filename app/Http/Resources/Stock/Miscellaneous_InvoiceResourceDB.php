<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Resources\Json\JsonResource;

class Miscellaneous_InvoiceResourceDB extends JsonResource
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
                'id' => $this->id,
                'user'=> $this->user,
                'img'=> $this->img,
                'img_three'=> $this->img_three,
                'img_two'=> $this->img_two  ,
                'img_thumbnail'=> $this->img_thumbnail  ,
                'supplier'=> $this->supplier  ,
                'supplier_invoice_number'=> $this->supplier_invoice_number , 
                'received_date'=> $this->received_date,  
                'total_price'=> $this->total_price,  
                'Note'=> $this->Note , 
                'paid'=> $this->paid,  
                'invoice_category'=> $this->invoice_category,  
                'invoice_type'=> $this->invoice_type,  
          
        ];
    }
    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }
}
