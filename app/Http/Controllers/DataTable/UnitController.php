<?php

namespace App\Http\Controllers\DataTable;

use App\Models\Stock\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UnitController extends DataTableController
{
    
    protected $allowCreation = true;

    protected $allowDeletion = true;

    public function builder()
    {
        return Unit::query();
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


        $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
        return "successfully updated";
        // return new PrivateUserResource($user);
    }
}
