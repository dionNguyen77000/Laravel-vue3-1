<?php

namespace App\Http\Controllers\DataTable;

use Image;

use App\Models\Role;
use App\Models\Stock\Unit;
use Illuminate\Http\Request;
use App\Models\Stock\Category;
use App\Models\Stock\Supplier;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Stock\Intermediate_product;
use App\Http\Resources\Stock\Intermediate_ProductResourceDB;


class Intermediate_ProductController extends DataTableController
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
          'unitOptions'=> $this->getUnitOptions(),
          'supplierOptions'=> $this->getSupplierOptions(),
          'categoryOptions'=> $this->getCategoryOptions(),
          'roleOptions'=> $this->getRoleOptions(),
          'statusOptions'=> $this->getStatusOptions(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]

      ]);
    }

    public function builder()
    {
        return Intermediate_Product::query();
    }

    
    public function getCustomColumnsNames()
    {
        return [
            'unit_id' => 'Unit',
            'category_id' => 'Category',
            'role_id' => 'Role',
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id','name', 'thumbnail','slug', 'price',
            'unit_id',
            'category_id',
            'description',
            'image',
            'current_qty',
            'prepared_point',
            'coverage',
            'required_qty',
            'role_id',
            'Status',
            'Active',
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            'name', 'slug', 'thumbnail','price','unit_id',
            'category_id', 
            'description',  
            'image',
            'current_qty',
            'prepared_point',
            'coverage',
            'required_qty',
            'role_id',
            'Status',
            'Active'
        ];
    }

    public function getCreatedColumns()
    {
        return [
            'name', 'slug', 'price','unit_id',
            'category_id', 
            'description',
            'current_qty',
            'prepared_point',
            'required_qty',
            'coverage',
            'role_id',
            'Status',
            'Active'
        ];
    }
    
    public function store(Request $request)

    {
        $this->validate($request, [
            'name' => 'required|unique:intermediate_products,name',
            'current_qty' => 'required',
            'prepared_point' => 'required',
            'coverage' => 'required',
        ]);

        $newI =  $this->builder->create($request->only($this->getCreatedColumns()));
        if($request->Status != 'Ongoing') {
            if ($request->current_qty <= $request->prepared_point){
                $newI->required_qty = $request->coverage - $request->current_qty;
                $newI->Status = 'Prepare';
                $newI->save();
            } else{
                $newI->required_qty = 0;
                $newI->Status = '';
                $newI->save();
            }
        }
      
       return $newI;
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:intermediate_products,name,' . $id,
        ]);

        // dd($request->current_qty);
        // $updated_intermediate = $this->builder->find($id);

        // dd($updated_intermediate);
        $intermediate = $this->builder->find($id);
        $updatedSuccess =  $intermediate ->update(
            $request->only($this->getUpdatableColumns())
        );

        if ($updatedSuccess == 1 & $intermediate->current_qty <= $intermediate->prepared_point){
            $intermediate->Status = 'Prepare';
            $intermediate->required_qty = $intermediate->coverage -   $intermediate->current_qty;  
            $intermediate->save();
        } elseif ($updatedSuccess == 1 & $intermediate->current_qty > $intermediate->prepared_point){
            $intermediate->Status = '';
            $intermediate->required_qty = 0;
            $intermediate->save();
        }

        return $updatedSuccess;
    }

    public function saveImage($id, Request $request)
    {
   
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);
        
        $image = request()->file('image');

        $imageNameResize = Image::make($image)
        ->resize(700, null, function ($constraint) {
            $constraint->aspectRatio();
        })
        ->encode('jpg');

        $thumbnailNameResize =  Image::make($image)
        ->fit(200,250)
        ->encode('jpg');

        $originName = $image->getClientOriginalName();
        // $thumbnailWithoutExtension = pathinfo($originName,PATHINFO_FILENAME);
        $imageName = time().'_'.$originName;
        $thumbnailName = time().'_thumbnail_'.$originName;
        Storage::put('public/intermediate_product_images/'. $imageName, $imageNameResize->__toString());
        Storage::put('public/intermediate_product_images/'. $thumbnailName, $thumbnailNameResize->__toString());

        $the_intermediate_product = $this->builder->find($id);

        if ($the_intermediate_product->thumbnail){
            $result_thumbnail_array = explode('/',$the_intermediate_product->thumbnail);
            $old_thumbnail_name = $result_thumbnail_array[count($result_thumbnail_array)-1];
            Storage::delete([
            'public/intermediate_product_images/'. $old_thumbnail_name,
            ]);
        }
        if ($the_intermediate_product->image){
            $result_image_array = explode('/',$the_intermediate_product->image);
            $old_image_name = $result_image_array[count($result_image_array)-1];
            Storage::delete([
            'public/intermediate_product_images/'. $old_image_name,
            ]);
        }
        // save new image
        $the_intermediate_product -> thumbnail = "/storage/intermediate_product_images/".$thumbnailName;
        $the_intermediate_product -> image = "/storage/intermediate_product_images/".$imageName;
        $the_intermediate_product -> save();

        return $the_intermediate_product;
        // return "successfully saved";
    }



    protected function getRecords(Request $request)
    {
        $builder = $this->builder;

        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }

        if (isset($request->category_id)) {
            $builder =   $builder->where('category_id','=',$request->category_id);
        }
        if (isset($request->role_id)) {
            $builder =   $builder->where('role_id','=',$request->role_id);
        }
        if (isset($request->Status)) {
            $builder =   $builder->where('Status','=',$request->Status);
        }

        try {
            return Intermediate_ProductResourceDB::collection(
                $builder->limit($request->limit)
                ->orderBy('id', 'asc')
                ->get($this->getDisplayableColumns())
            );
            
          
        } catch (QueryException $e) {
            return [];
        }    
    }

    public function getUnitOptions()
    {
        $c = Unit::all('id','name');
        $returnArr = [];
        foreach ($c as  $sc) {
            $returnArr[$sc['id']] = $sc['name'];
        }
        return $returnArr;
    }

    public function getSupplierOptions()
    {
        $c = Supplier::all('id','name');

        $returnArr = [];
        foreach ($c as  $sc) {
            $returnArr[$sc['id']] = $sc['name'];
        }
        return $returnArr;
    }

    public function getCategoryOptions()
    {
        $c = Category::all('id','name');

        $returnArr = [];
        foreach ($c as  $sc) {
            $returnArr[$sc['id']] = $sc['name'];
        }
        return $returnArr;
    }
    public function getRoleOptions()
    {
        $r = Role::all('id','name');

        $returnArr = [];
        foreach ($r as  $sr) {
            $returnArr[$sr['id']] = $sr['name'];
        }
        return $returnArr;
    }
    public function getStatusOptions()
    {
        $returnArr = ['Prepare'];
        return $returnArr;
    }
   

}
