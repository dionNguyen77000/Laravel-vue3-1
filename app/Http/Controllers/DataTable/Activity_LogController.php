<?php

namespace App\Http\Controllers\DataTable;

use App\Models\User;
use App\Models\Activity_Log;
use App\Http\Resources\Admin\Activity_LogResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Activity_LogController extends DataTableController
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
          'userOptions'=> $this->getUserOptions(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]
      ]);
    }

    public function builder()
    {
        return Activity_Log::query();
    }

    public function getCustomColumnsNames()
    {
        return [
           
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id',
            'description',
            'subject_type',
            'subject_id',
            'causer_id',
            'properties',
            'created_at',
            'updated_at',
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            'id',
            'description',
            'subject_type',
            'subject_id',
            'causer_id',
            'properties',
            'created_at',
            'updated_at',
        ];
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'log_name' => 'required|unique:units,log_name',
        ]);

        $this->builder->create($request->only($this->getUpdatableColumns()));
    }

    protected function getRecords(Request $request)
    {
        $builder = $this->builder;
        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }

        // if (isset($request->type) && $request->type!='') {
        //     $builder = $builder->where('type','=',$request->type);
        // }        


        try {
            return Activity_LogResource::collection(
                 $builder->limit($request->limit)
                 ->orderBy('id', 'asc')
                 ->get($this->getDisplayableColumns())
            );
         } catch (QueryException $e) {
             return [];
         }    
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'log_name' => 'required|unique:units,log_name,' . $id,
        ]);

        return $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
    }

    public function getUserOptions()
    {
        $r = User::all('id','name');

        $returnArr = [];
        foreach ($r as  $sr) {
            $returnArr[$sr['id']] = $sr['name'];
        }
        return $returnArr;
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
