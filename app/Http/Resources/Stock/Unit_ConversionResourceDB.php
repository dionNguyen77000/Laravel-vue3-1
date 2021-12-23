<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Resources\Json\JsonResource;

class Unit_ConversionResourceDB extends JsonResource
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
            'intermediate_product_id'=>$this->intermediate_product_id,
            'ip_unit'=>$this->intermediate_product->unit->name,
            'goods_material_id' => $this->goods_material_id,
            'gm_unit'=>$this->goods_material->unit->name,
            'rate' => $this->rate,   
            
    
        ];
    }
    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }
}
