<?php

namespace App\Http\Controllers\DataTable;

use App\Models\Stock\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UnitController extends DataTableController
{
    
    protected $allowCreation = true;

    protected $allowDeletion = true;

    public function index(Request $request)
    {
    //   return $this->builder->get();
      return response()->json([
        'data' => [
          'table' => $this->builder->getModel()->getTable(),
          'displayable' => array_values($this->getDisplayableColumns()),
          'updatable' => array_values($this->getUpdatableColumns()),
          'records' => $this->getRecords($request),
          'custom_columns' => $this->getCustomColumnsNames(),
          'userPermissionOptions'=> $this->getUserPermissionOptions(),
          'userRoleOptions'=> $this->getUserRoleOptions(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]
      ]);
    }

    public function builder()
    {
        return Unit::query();
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
            'name' => 'required|unique:units,name',
        ]);

        $this->builder->create($request->only($this->getUpdatableColumns()));
        return "successfully created";
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:units,name,' . $id,
        ]);

        return $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
    }

    public function getUserPermissionOptions()
    {
        $user = auth()->user();
        
        return $user->getPermissions();

    }
   
    public function getUserRoleOptions()
    {
        $user = auth()->user();       
        return $user->roles->map->only(['id', 'name']);
    }
}
