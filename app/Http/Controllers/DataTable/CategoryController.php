<?php

namespace App\Http\Controllers\DataTable;

use Illuminate\Http\Request;
use App\Models\Stock\Category;
use App\Http\Controllers\Controller;


class CategoryController extends DataTableController
{
    protected $allowCreation = true;

    protected $allowDeletion = true;

    public function builder()
    {
        return Category::query();
    }

    public function getDisplayableColumns()
    {
        return [
            'id','name', 'slug', 'parent_id'
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            'name', 'slug', 'parent_id'
        ];
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
        ]);

        $this->builder->create(
            [
                'name' => $request->name,
                'slug' => $request->slug,
                'parent_id' => $request->parent_id,            ]
        );
        return "successfully created";
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $id,
        ]);


        $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
        return "successfully updated";
        // return new PrivateUserResource($user);
    }
}
