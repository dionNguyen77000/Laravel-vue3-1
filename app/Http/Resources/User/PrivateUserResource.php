<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class PrivateUserResource extends JsonResource
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
            'name' => $this ->name,
            'username' => $this->username,
            'email' =>$this -> email,
            // 'roles' => $this->roles->pluck('name','id'),
            'Active' =>  $this->Active,
            'roles' => $this->roles->map->only(['id', 'name']),
            // 'roles' => $this->getRoleNames(),
            'permissions' =>  $this->getPermissions(),
            // 'intermediateProducts' =>  $this->getIntermediateProducts(),
        ];
    }
}
