<?php

namespace App\Http\Controllers\DataTable;

use Image;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Stock\Unit;
use Illuminate\Http\Request;
use App\Models\Stock\Category;
use App\Models\Stock\Supplier;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
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
          'permissionOptions'=> $this->getPermissionOptions(),
          'PreparationOptions'=> $this->getPreparationOptions(),
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
            'permission_id' => 'Permission',
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id','name', 'thumbnail','slug', 'price', 'unit_id',
            'category_id',
            'description',
            'image',
            'current_qty',
            'prepared_point',
            'coverage',
            'required_qty',
            'permission_id',
            'Preparation',
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
            'permission_id',
            'Preparation',
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
            'permission_id',
            'Preparation',
            'Active'
        ];
    }
    
    public function store(Request $request)

    {
        // dd($request->permission_id);
        $this->validate($request, [
            'name' => 'required|unique:intermediate_products,name',
            'slug' => 'unique:intermediate_products,slug',
            'price' => 'numeric',
            'current_qty' => 'required|numeric',
            'prepared_point' => 'required|numeric',
            'coverage' => 'required|numeric',
        ]);

        $newI =  $this->builder->create($request->only($this->getCreatedColumns()));
        if($request->Preparation != 'OnGoing') {
            if ($request->current_qty <= $request->prepared_point){
                $newI->required_qty = $request->coverage - $request->current_qty;
                $newI->Preparation = 'Yes';
                $newI->save();
            } else{
                $newI->required_qty = 0;
                $newI->Preparation = 'No';
                $newI->save();
            }
        }
      
       return $newI;
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:intermediate_products,name,' . $id,
            'price' => 'numeric',
            'current_qty' => 'required|numeric',
            'prepared_point' => 'required|numeric',
            'coverage' => 'required|numeric',
        ]);

        // dd($request->current_qty);
        $intermediate = $this->builder->find($id);
        // dd($intermediate->Preparation);
        if ($intermediate->Preparation == 'OnGoing'){
            return 'Error - OnGoing Update';
        }
        $updatedSuccess =  $intermediate ->update(
            $request->only($this->getUpdatableColumns())
        );

        if ($updatedSuccess == 1 & $intermediate->current_qty <= $intermediate->prepared_point){
            $intermediate->Preparation = 'Yes';
            $intermediate->required_qty = $intermediate->coverage -   $intermediate->current_qty;  
            $intermediate->save();
        } elseif ($updatedSuccess == 1 & $intermediate->current_qty > $intermediate->prepared_point){
            $intermediate->Preparation = 'No';
            $intermediate->required_qty = 0;
            $intermediate->save();
        }

        return $updatedSuccess;
    }

    public function destroy($ids, Request $request)
    {
        if (!$this->allowDeletion) {
            return;
        }

        $arrayIds = explode(',',$ids);

        if (count($arrayIds) > 1 ) {
            $this->builder->whereIn('id', explode(',', $ids))->delete();
        } else if (count($arrayIds) == 1){
            $inter_p = Intermediate_product::withCount('daily_emp_works')->find($ids);
            if($inter_p->daily_emp_works_count == 0){
                $inter_p->delete();
                return ('deleted');
            } else {
                // return ('deleted')->setPreparationCode(422);
                return response(array(
                    'message' => 'Foreign Key Problem',
                    'error' => 'Cannot delete this product, this product has been used in Daily Emp Work. You need to delete them first in Daily Emp Work before you can delete here.',
                 ), 422);
            }

            // dd($this->builder->find($ids)->with(['daily_emp_works']));
            // $this->builder->find($ids)->delete();
        }
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

        /**
    * Get all values from specific key in a multidimensional array
    *
    * @param $key string
    * @param $arr array
    * @return null|string|array
    */
    public function array_value_recursive($key, array $arr){
        $val = array();
        array_walk_recursive($arr, function($v, $k) use($key, &$val){
            if($k == $key) array_push($val, $v);
        });
        return count($val) > 1 ? $val : array_pop($val);
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
        if (isset($request->permission_id)) {
            if($request->permission_id == 'All'){
                $user = auth()->user();
                $userPermissions = $user->getPermissions();
                $userPermissionIds = array();
                $key = 'id';
                array_walk_recursive($userPermissions, function($v, $k) use($key, &$userPermissionIds){
                    if($k == $key) array_push($userPermissionIds, $v);
                });
                // dd($userPermissionIds);
                $builder =   $builder->whereIn('permission_id',$userPermissionIds);

            } else {
                // dd($request->permission_id);
                $builder =   $builder->where('permission_id','=',$request->permission_id);
            }
        }

        if (isset($request->Preparation)) {
            // dd($request->Preparation);
            if( strpos($request->Preparation,',' ) !== false ) {
                
                $arrayPreps = explode(',',$request->Preparation);
                // dd( $arrayPreps[0]);
                // dd( $arrayPreps[1]);
                $builder =  $builder->whereIn('Preparation',$arrayPreps);
                                   
           } else 
            $builder =   $builder->where('Preparation','=',$request->Preparation);
        }

        if (isset($request->Active)) {
            $builder =   $builder->where('Active','=',$request->Active);
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
    public function getPermissionOptions()
    {
        $r = Permission::all('id','name');

        $returnArr = [];
        foreach ($r as  $sr) {
            $returnArr[$sr['id']] = $sr['name'];
        }
        return $returnArr;
    }
    public function getPreparationOptions()
    {
        $returnArr = ['Yes'];
        return $returnArr;
    }

    
    public function sendMail()
    {   
        return 'not work';
        dd(storage_path('img/logo_backend.png'));
        $data = [
            'email' => 'anhduc.nguyen77000@gmail.co',
            'title' => 'Mail from Golden Lor Yarrabilba',
            'body' => 'This is for testing mail with attachment using gmail'
        ];

        $files = [
            storage_path('img/logo_backend.png')
        ];
        Mail::send('emails.myTestMail', $data, function($message)use($data, $files) {
            $message->to($data["email"], $data["email"])
                    ->subject($data["title"]);
 
            foreach ($files as $file){
                $message->attach($file);
            }
            
        });
        // Mail::to('anhduc.nguyen77000@gmail.com')->send(new MyTestMail($details));

        dd("Email is Sent ok ok.");
    }
   

}
