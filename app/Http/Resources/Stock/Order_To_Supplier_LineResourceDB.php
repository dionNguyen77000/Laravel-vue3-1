<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Resources\Json\JsonResource;

class Order_To_Supplier_LineResourceDB extends JsonResource
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
                'orders_to_supplier_id'=> $this->orders_to_supplier_id,
                // 'user_role'=>$this->user->roles,
                // 'goods_material_id'=> $this->goods_material_id,
                'goods_material'=> $this->goods_material,
                'unit'=> $this->unit,
                'o_unit_quantity'=> $this->o_unit_quantity,
                'o_unit_price'=> $this->o_unit_price  ,
                'o_line_price'=> $this->o_line_price , 
                'category'=> $this->category  
          
        ];
    }

    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }
}
