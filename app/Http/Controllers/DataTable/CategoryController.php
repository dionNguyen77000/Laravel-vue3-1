<?php

namespace App\Http\Controllers\DataTable;

use Illuminate\Http\Request;
use App\Models\Stock\Category;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Http\Resources\Stock\CategoryResourceDB;


class CategoryController extends DataTableController
{
    protected $allowCreation = true;

    protected $allowDeletion = true;

    
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
          'categoryParentOptions'=> $this->getcategoryParentOptions(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]

      ]);
    }

    public function builder()
    {
        return Category::query();
    }

    public function getCustomColumnsNames()
    {
        return [
            'parent_id' => 'Parent Category',
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id','name', 'type', 'parent_id'
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            'name', 'type', 'parent_id'
        ];
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:categories,name',
        ]);

        // dd($request);
        $this->builder->create($request->only($this->getUpdatableColumns()));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:categories,name,' . $id,
        ]);


        $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
        // return new PrivateUserResource($user);
    }

    protected function getRecords(Request $request)
    {
        // //   // get highest level Parents
        // return CategoryResource::collection(
        //     // Category::with('children')->parentHighest()->ordered()->get()
        //     Category::get()
        //     // Category::all()
        // );
            // return $this->builder->get();

        //    dd (Category::get());
        $builder = $this->builder;

        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }

        if (isset($request->type) && $request->type!='') {
            $builder =   $builder->where('type','=',$request->type);
        }

        try {
           return CategoryResourceDB::collection(
                $builder->limit($request->limit)->orderBy('id', 'asc')->get($this->getDisplayableColumns())
           );
        } catch (QueryException $e) {
            return [];
        }    
    }

    public function getcategoryParentOptions()
    {
        
          // get highest level Parents
        $v = Category::all('id','name');

        $returnArr = [];
        foreach ($v as  $i) {
            $returnArr[$i['id']] = $i['name'];
        }
        return $returnArr;
        // return $returnArr;
    }

}
