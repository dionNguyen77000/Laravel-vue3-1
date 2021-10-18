<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;


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
            // 'slug' => $this->slug,
            'img_thumbnail' => $this->img_thumbnail,
            'price' => $this->price,
            // 'price' => $this->formattedPrice,
            'unit_id' => $this->unit_id,
            // 'unit_name' => is_null($this->unit) ? '' : $this->unit['name'],
            'supplier_id' => $this->supplier_id,
            // 'supplier_name' => is_null($this->supplier) ? '' : $this->supplier['name'],
            'category_id' => $this->category_id,
            // 'category_name' => is_null($this->category) ? '' : $this->category['name'],
            // 'description' => $this->description,
            'current_qty'=> $this->current_qty,
            'prepared_point'=> $this->prepared_point,
            'coverage'=> $this->coverage,
            'required_qty'=> $this->required_qty,
            // 'permission_id'=> $this->permission_id,
            'Preparation'=> $this->Preparation,
            'Active'=> $this->Active,
            'img' => $this->img,
            'img_two' => $this->img_two,
            'img_three' => $this->img_three,
            'location_id' => $this->location_id,
            'permissions' =>  $this->permissions->map->only(['id', 'name']),   
       
        ];
    }

    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }
}
