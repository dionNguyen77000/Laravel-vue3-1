<?php

namespace App\Http\Controllers\DataTable;

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User\RoleResource;

class PermissionController extends DataTableController
{
    protected $allowCreation = true;

    protected $allowDeletion = true;

    public function builder()
    {
        return Permission::query();
    }

    public function getCustomColumnsNames()
    {
        return [
           
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id','name'
        ];
    }
    public function getUpdatableColumns()
    {
        return [
           'name'
        ];
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:permissions,name',
        ]);
        return $this->builder->create($request->only($this->getUpdatableColumns()));
       
    }


    public function update($id, Request $request)
    {

        $this->validate($request, [
            'name' => 'required|unique:permissions,name,' . $id ,
            // 'created_at' => 'date'
        ]);

       
        return $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
        

        // return new PrivateUserResource($user);
    }
}

