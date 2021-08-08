<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Resources\Json\JsonResource;

class Daily_Emp_WorkResourceDB extends JsonResource
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
                'date' => $this->date,
                'user_id'=> $this->user_id,
                // 'user_role'=>$this->user->roles,
                'intermediate_product_id'=> $this->intermediate_product_id,
                'done_qty'=> $this->done_qty,
                'intermediate_product_id'=> $this->intermediate_product_id,
                'current_prepared_qty'=> $this->current_prepared_qty,
                'required_qty'=> $this->required_qty,
                'role_id'=> $this->intermediate_product->role_id,
                'Status'=> $this->Status,
                'Note'=> $this->Note,
               
    //         'id'=>$this->id,
    //         'name' => $this->name,
    //         'slug' => $this->slug,
    //         'thumbnail' => $this->thumbnail,
    //         'price' => $this->price,
    //         'unit_id' => $this->unit_id,
    //         // 'unit_name' => is_null($this->unit) ? '' : $this->unit['name'],
    //         'category_id' => $this->category_id,
    //         // 'category_name' => is_null($this->category) ? '' : $this->category['name'],
    //         'description' => $this->description,
    //         'current_qty'=> $this->current_qty,
    //         'prepared_point'=> $this->prepared_point,
    //         'coverage'=> $this->coverage,
    //         'required_qty'=> $this->required_qty,
    //         'role_id'=> $this->role_id,
    //         'Status'=> $this->Status,
    //         'Active'=> $this->Active,
    //         'image' => $this->image,
          
        ];
    }

    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }
}
