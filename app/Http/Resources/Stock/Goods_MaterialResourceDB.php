<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Resources\Json\JsonResource;

class Goods_MaterialResourceDB extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'thumbnail' => $this->thumbnail,
            'price' => $this->formattedPrice,
            'unit_id' => $this->unit_id,
            // 'unit_name' => is_null($this->unit) ? '' : $this->unit['name'],
            'supplier_id' => $this->supplier_id,
            // 'supplier_name' => is_null($this->supplier) ? '' : $this->supplier['name'],
            'category_id' => $this->category_id,
            // 'category_name' => is_null($this->category) ? '' : $this->category['name'],
            'description' => $this->description,
            'image' => $this->image,
          
        ];
    }

    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }
}
