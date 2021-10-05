<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Resources\Json\JsonResource;

class Compare_Order_InvoiceResourceDB extends JsonResource
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
                'goods_material'=> $this->goods_material,
                'unit'=> $this->unit,
                'o_unit_quantity' => $this->o_unit_quantity,
                'i_unit_quantity'=> $this->i_unit_quantity,
                'o_unit_price'=> $this->o_unit_price,
                'i_unit_price'=> $this->i_unit_price  ,
                'o_line_price'=> $this->o_line_price,
                'i_line_price'=> $this->i_line_price , 
                'category'=> $this->category,
          
        ];
    }

    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }
}
