<?php

namespace App\Http\Controllers\DataTable;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User\RoleResource;
use App\Http\Resources\Stock\RoleResourceDB;

class RoleController extends DataTableController
{
    protected $allowCreation = true;

    protected $allowDeletion = true;

    public function builder()
    {
        return Role::query();
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

    public function getCreatedColumns()
    {
        return [
            'name'
        ];
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
        ]);

        $newRole =  $this->builder->create($request->only($this->getCreatedColumns()));
        if($request->assignedPermissionIds && count($request->assignedPermissionIds) > 0 ) {
            $newRole->permissions()->attach($request->assignedPermissionIds);
        }
       return $newRole;       
    }


    public function update($id, Request $request)
    {

        $this->validate($request, [
            'name' => 'required|unique:roles,name,' . $id,
            // 'created_at' => 'date'
        ]);

       $theRole = $this->builder->find($id);
       $updatedSuccess =  $theRole ->update(
            $request->only($this->getUpdatableColumns()),
        );
        $theRole->permissions()->sync($request->assignedPermissionIds);
        
        return $theRole->load('permissions');
        

        // return new PrivateUserResource($user);
    }

    public function index(Request $request)
    {
    //   return $this->builder->get();
      return response()->json([
        'data' => [
          'table' => $this->builder->getModel()->getTable(),
          'db_column_name' =>array_values($this->getDatabaseColumnNames()),
          'displayable' => array_values($this->getDisplayableColumns()),
          'updatable' => array_values($this->getUpdatableColumns()),
          'records' => $this->getRecords($request),
          'custom_columns' => $this->getCustomColumnsNames(),
          'permissionOptions'=> $this->getPermissionOptions(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]

      ]);
    }

    public function getPermissionOptions()
    {
        $r = Permission::all('id','name');

        $returnArr = [];
        foreach ($r as  $sr) {
            $returnArr[$sr['id']] = $sr['name'];
        }
        return $returnArr;
    }

    protected function getRecords(Request $request)
    {
        $builder = $this->builder;

        try {
            return RoleResourceDB::collection(
                $builder->limit($request->limit)
                ->orderBy('id', 'asc')
                ->get($this->getDisplayableColumns())
            );
            
          
        } catch (QueryException $e) {
            return [];
        }    
    }
}

