<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResourceDB extends JsonResource
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
    //         'name' => $this->name,
    //         'slug' => $this->slug,
    //         'parent_id' => $this->parent_id,
    //         // 'children' => CategoryResource::collection($this->whenLoaded('children')),
    //         // 'parent_id' => is_null($this->parentCat) ? '' : $this->parentCat,
    //         // isset($this->parentCat) ? null :  $this->parentCat['name']
    //     ];

        
    // }

    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
