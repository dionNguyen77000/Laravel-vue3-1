<?php

namespace App\Http\Controllers\DataTable;

use Illuminate\Http\Request;
use App\Models\Stock\Supplier;
use App\Http\Controllers\Controller;

class SupplierController extends DataTableController
{
    protected $allowCreation = true;

    protected $allowDeletion = true;

    public function builder()
    {
        return Supplier::query();
    }

    public function getDisplayableColumns()
    {
        return [
            'id','name', 'group', 'representative','phone','address','description',
            'email'
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            'name', 'group', 'representative','phone','address','description','email'
        ];
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:suppliers,name',
            'phone' => 'required',
            'address' => 'required',
            'email' => 'required|unique:suppliers,email',
        ]);
        $this->builder->create($request->only($this->getUpdatableColumns()));
        return "successfully created";
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:suppliers,name,' . $id,
            'phone' => 'required',
            'address' => 'required',
            'email' => 'required|unique:suppliers,email,' . $id . '|email',
        ]);


        $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
        return "successfully updated";
        // return new PrivateUserResource($user);
    }
}
