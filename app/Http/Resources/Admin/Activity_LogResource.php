<?php

namespace App\Http\Resources\Admin;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class Activity_LogResource extends JsonResource
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
    //         'id'=>$this->id,
    //         'name' => $this->name,
    //         'slug' => $this->slug,
    //         'parent_id' => $this->parent_id,
    //         // 'children' => CategoryResource::collection($this->whenLoaded('children')),
    //         // 'parent_id' => is_null($this->parentCat) ? '' : $this->parentCat,
    //         // isset($this->parentCat) ? null :  $this->parentCat['name']

                'id'=> $this->id,
                'description' => $this->description,
                'subject_type' => $this->subject_type,
                'subject_id' => $this->subject_id,
                'causer_id' => $this->causer_id,
                'properties' => $this->properties,
                'created_at' => Carbon::parse($this->created_at)->format('d-M-Y H:i:s'),          
                'updated_at' => Carbon::parse($this->updated_at)->format('d-M-Y H:i:s'),
        ];

        
    }

    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }
}
