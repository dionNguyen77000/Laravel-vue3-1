<?php

namespace App\Http\Controllers\DataTable;

use Image;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Stock\Unit;
use Illuminate\Http\Request;
use App\Models\Stock\Category;
use App\Models\Stock\Location;
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
          'locationOptions'=> $this->getLocationOptions(),
          'permissionOptions'=> $this->getPermissionOptions(),
          'userPermissionOptions'=> $this->getUserPermissionOptions(),
          'userRoleOptions'=> $this->getUserRoleOptions(),        
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
            'img_thumbnail'=>'image',
            'current_qty' => 'cur_qty',
            'prepared_point'=>'Prep_Point',
            'Preparation' => 'Prep',
            'required_qty'=> 'Required',
            'location_id'=> 'Location',
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id','name', 'img_thumbnail',
            // 'slug', 
            // 'price', 
            'unit_id',
            'category_id',
            'img',
            'current_qty',
            'prepared_point',
            'coverage',
            'required_qty',
            'Preparation',
            'Active',
            'location_id',
            'permissions',
        ];
    }

    
    public function getRetrievedColumns()
    {
        return [

            'id',
            'name', 
            'img',
            'img_three',
            'img_two',
            'img_thumbnail',
            // 'slug', 
            // 'price',
            'unit_id',
            'category_id',
            'current_qty',
            'prepared_point',
            'coverage',
            'required_qty',
            'Preparation',
            'Active',
            'location_id',
        ];
    }

    public function getUpdatableColumns()
    {
        return [
            'name', 
            // 'slug', 
            'img_thumbnail',
            // 'price',
            'unit_id',
            'category_id', 
            'img',
            'current_qty',
            'prepared_point',
            'coverage',
            'required_qty',
            'Preparation',
            'Active',
            'location_id',
            'permissions',
        ];
    }

    public function getCreatedColumns()
    {
        return [
            'name', 
            // 'slug', 
            'price',
            'unit_id',
            'category_id', 
            'current_qty',
            'prepared_point',
            'required_qty',
            'coverage',
            'Preparation',
            'Active',
            'location_id',
        ];
    }
    
    public function store(Request $request)

    {
        // dd($request->permission_id);
        $this->validate($request, [
            'name' => 'required|unique:intermediate_products,name',
            // 'slug' => 'unique:intermediate_products,slug',
            'current_qty' => 'required|numeric',
            'prepared_point' => 'required|numeric',
            'coverage' => 'required|numeric',
        ]);
       
        $newI = new Intermediate_product();
        $newI->name = $request->name;
        $newI->price = $request->price;
        $newI->unit_id = $request->unit_id;
        $newI->category_id = $request->category_id;
        $newI->current_qty = $request->current_qty;
        $newI->prepared_point = $request->prepared_point;
        $newI->coverage = $request->coverage;
        $newI->Active = $request->Active;
        $newI->location_id = $request->location_id;

        // $newI =  $this->builder->create($request->only($this->getCreatedColumns()));

        if($request->Preparation != 'OnGoing') {

            if ($request->current_qty < $request->prepared_point){
                $newI->required_qty = $request->coverage - $request->current_qty;
                $newI->Preparation = 'Yes';
            } elseif ($request->current_qty >= $request->prepared_point) {
                $newI->required_qty = 0;
                $newI->Preparation = 'No';
            }
        }     
        $newI->save();
         
        if($request->assignedPermissionIds && count($request->assignedPermissionIds) > 0 ) {
            $newI->permissions()->attach($request->assignedPermissionIds);
        }
       return $newI;
    }
    public function updateCurrentQty($id,Request $request){
        // dd($request->current_qty);

        $this->validate($request, [
            'current_qty' => 'required|numeric',
        ]);
        $IP =  $this->builder->find($id);
        $IP->current_qty = $request->current_qty;
          //update Preparation according to current_qty, Prepared_Point And Coverage
          if ($IP->current_qty <= $IP->prepared_point){
            $IP->Preparation = 'Yes';
            $IP->required_qty = $IP->coverage -   $IP->current_qty;  
           
        } else{
            $IP->Preparation = 'No';
            $IP->required_qty = 0;        
        } 
        $IP->save();       

        // dd($request->current_qty);
    }

    public function update($id, Request $request)
    {
        // dd($request->assignedPermissionIds);
        $this->validate($request, [
            'name' => 'required|unique:intermediate_products,name,' . $id,
            // 'price' => 'numeric',
            'current_qty' => 'required|numeric',
            'prepared_point' => 'required|numeric',
            'coverage' => 'required|numeric',
            'assignedPermissionIds' => 'required',
        ]);

        
        $intermediate = $this->builder->find($id);
        
        if ($intermediate->Preparation == 'OnGoing'){
            return 'Error - OnGoing Update';
        }

        $intermediate->name = $request->name;
        $intermediate->unit_id = $request->unit_id;
        $intermediate->category_id = $request->category_id;
        $intermediate->current_qty = $request->current_qty;
        $intermediate->prepared_point = $request->prepared_point;
        $intermediate->coverage = $request->coverage;
        $intermediate->Active = $request->Active;
        $intermediate->location_id = $request->location_id;

        //update the permissions of the intermediate
        $intermediate->permissions()->sync($request->assignedPermissionIds);

        if ($intermediate->current_qty < $intermediate->prepared_point){
            $intermediate->Preparation = 'Yes';
            $intermediate->required_qty = $intermediate->coverage -  $intermediate->current_qty;  
        } elseif ($intermediate->current_qty >= $intermediate->prepared_point){
            $intermediate->Preparation = 'No';
            $intermediate->required_qty = 0;
        } 

        $intermediate->save();

        return $intermediate;
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

    public function saveImage_origin($id, Request $request)
    {
   
        $silderImageidArray = explode('-',$silderImageids);

        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);
        
        $image = request()->file('image');

        $imageNameResize = Image::make($image)
        ->resize(700, 600, function ($constraint) {
            $constraint->aspectRatio();
        })
        ->encode('jpg');

        $thumbnailNameResize =  Image::make($image)
        ->fit(200,250)
        ->encode('jpg');
     
        $originName = $image->getClientOriginalName();
        // dd($originName);
        // $thumbnailWithoutExtension = pathinfo($originName,PATHINFO_FILENAME);
        // $imageName = time().'_'.$originName;
        // $imageName = time().'.jpg';
        // $thumbnailName = time().'_thumbnail_'.$originName;
        // $thumbnailName = time().'_thumbnail_'.'.jpg';
        // Storage::put("public/intermediate_product_images/". $imageName, $imageNameResize->__toString());
        // Storage::put("public/intermediate_product_images/". $thumbnailName, $thumbnailNameResize->__toString());

        $dateInBrisbane= Carbon::now('Australia/Brisbane');
        $receivedDate=$dateInBrisbane->isoFormat('MMMDoYY'); 

        $the_inter= $this->builder->find($silderImageidArray[0]);

        // deal with second or third invoice image
        if(count($silderImageidArray)>1){
            $img_number = $silderImageidArray[1];
            $imageName = $the_g_m->name.'_'.$img_number.'.jpg';


            //delete old image
            if ($img_number == 2) {
                if ($the_g_m->img_two){
                    $result_image_array = explode('/',$the_g_m->img_two);
                    $old_image_name = $result_image_array[count($result_image_array)-1];
                    Storage::delete([
                    "public/intermediate_product_images/$the_g_m->name/". $old_image_name,
                    ]);
                }
                Storage::put("public/intermediate_product_images/$the_g_m->name/". $imageName, $imageNameResize->__toString());
                // save new image path to database
                $the_g_m -> img_two = "/storage/intermediate_product_images/$the_g_m->name/".$imageName;
                $the_g_m -> save();
            } else if ($img_number == 3) {
                if ($the_g_m->img_three){
                    $result_image_array = explode('/',$the_g_m->img_three);
                    $old_image_name = $result_image_array[count($result_image_array)-1];
                    Storage::delete([
                    "public/intermediate_product_images/$the_g_m->name/". $old_image_name,
                    ]);
                }
                Storage::put("public/intermediate_product_images/$the_g_m->name/". $imageName, $imageNameResize->__toString());
                $the_g_m -> img_three = "/storage/intermediate_product_images/$the_g_m->name/".$imageName;
                $the_g_m -> save();
            }
          
        }        
        // deal with first image 
        else {
           
            //delete old image in folder
            if ($the_g_m->img_thumbnail){
                $result_thumbnail_array = explode('/',$the_g_m->img_thumbnail);
                $old_thumbnail_name = $result_thumbnail_array[count($result_thumbnail_array)-1];
                Storage::delete([
                "public/intermediate_product_images/$the_g_m->name/". $old_thumbnail_name,
                ]);
            }
            if ($the_g_m->img){
                $result_image_array = explode('/',$the_g_m->img);
                $old_image_name = $result_image_array[count($result_image_array)-1];
                Storage::delete([
                "public/intermediate_product_images/$the_g_m->name/". $old_image_name,
                ]);
            }

            
            $imageName = $the_g_m->name.'.jpg';
            $thumbnailName = $the_g_m->name.'_thumbnail_'.'.jpg';

            Storage::put("public/intermediate_product_images/$the_g_m->name/". $imageName, $imageNameResize->__toString());
            Storage::put("public/intermediate_product_images/$the_g_m->name/". $thumbnailName, $thumbnailNameResize->__toString());

            // save new image
            $the_g_m -> img_thumbnail = "/storage/intermediate_product_images/$the_g_m->name/".$thumbnailName;
            $the_g_m -> img = "/storage/intermediate_product_images/$the_g_m->name/".$imageName;
            $the_g_m -> save();
        }
        return $the_g_m;
    }

    public function saveImage($silderImageids, Request $request)
    {
        $silderImageidArray = explode('-',$silderImageids);

        // dd($silderImageidArray);
       
        
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);
        
        $image = request()->file('image');

        $imageNameResize = Image::make($image)
        ->resize(700, 600, function ($constraint) {
            $constraint->aspectRatio();
        })
        ->encode('jpg');

        $thumbnailNameResize =  Image::make($image)
        ->fit(200,250)
        ->encode('jpg');
     
        $originName = $image->getClientOriginalName();
        // dd($originName);
        // $thumbnailWithoutExtension = pathinfo($originName,PATHINFO_FILENAME);
        // $imageName = time().'_'.$originName;
        // $imageName = time().'.jpg';
        // $thumbnailName = time().'_thumbnail_'.$originName;
        // $thumbnailName = time().'_thumbnail_'.'.jpg';
        // Storage::put("public/intermediate_product_images/". $imageName, $imageNameResize->__toString());
        // Storage::put("public/intermediate_product_images/". $thumbnailName, $thumbnailNameResize->__toString());

        $dateInBrisbane= Carbon::now('Australia/Brisbane');
        $receivedDate=$dateInBrisbane->isoFormat('MMMDoYY'); 

        $the_inter= $this->builder->find($silderImageidArray[0]);

        // deal with second or third invoice image
        if(count($silderImageidArray)>1){
            $img_number = $silderImageidArray[1];
            $imageName = $the_inter->name.rand(1,999).'_'.$img_number.'.jpg';


            //delete old image
            if ($img_number == 2) {
                if ($the_inter->img_two){
                    $result_image_array = explode('/',$the_inter->img_two);
                    $old_image_name = $result_image_array[count($result_image_array)-1];
                    Storage::delete([
                    "public/intermediate_product_images/". $old_image_name,
                    ]);
                }
                Storage::put("public/intermediate_product_images/". $imageName, $imageNameResize->__toString());
                // save new image path to database
                $the_inter -> img_two = "/storage/intermediate_product_images/".$imageName;
                $the_inter -> save();
            } else if ($img_number == 3) {
                if ($the_inter->img_three){
                    $result_image_array = explode('/',$the_inter->img_three);
                    $old_image_name = $result_image_array[count($result_image_array)-1];
                    Storage::delete([
                    "public/intermediate_product_images/". $old_image_name,
                    ]);
                }
                Storage::put("public/intermediate_product_images/". $imageName, $imageNameResize->__toString());
                $the_inter -> img_three = "/storage/intermediate_product_images/".$imageName;
                $the_inter -> save();
            }
          
        }        
        // deal with first image 
        else {
           
            //delete old image in folder
            if ($the_inter->img_thumbnail){
                $result_thumbnail_array = explode('/',$the_inter->img_thumbnail);
                $old_thumbnail_name = $result_thumbnail_array[count($result_thumbnail_array)-1];
                Storage::delete([
                "public/intermediate_product_images/". $old_thumbnail_name,
                ]);
            }
            if ($the_inter->img){
                $result_image_array = explode('/',$the_inter->img);
                $old_image_name = $result_image_array[count($result_image_array)-1];
                Storage::delete([
                "public/intermediate_product_images/". $old_image_name,
                ]);
            }

            $randomNumber = rand(1,999);            
            $imageName = $the_inter->name.$randomNumber.'.jpg';
            $thumbnailName = $the_inter->name.'_thumbnail_'.$randomNumber.'.jpg';

            Storage::put("public/intermediate_product_images/". $imageName, $imageNameResize->__toString());
            Storage::put("public/intermediate_product_images/". $thumbnailName, $thumbnailNameResize->__toString());

            // save new image
            $the_inter -> img_thumbnail = "/storage/intermediate_product_images/".$thumbnailName;
            $the_inter -> img = "/storage/intermediate_product_images/".$imageName;
            $the_inter -> save();
        }
        return $the_inter;
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

        if (isset($request->category_id) && $request->category_id != 'All') {
            $builder =   $builder->where('category_id','=',$request->category_id);
        }
        if (isset($request->location_id) && $request->location_id != 'All') {
            $builder =   $builder->where('location_id','=',$request->location_id);
        }
        // if (isset($request->permission_id)) {
        //     if($request->permission_id == 'All'){
        //         $user = auth()->user();
        //         $userPermissions = $user->getPermissions();
        //         $userPermissionIds = array();
        //         $key = 'id';
        //         array_walk_recursive($userPermissions, function($v, $k) use($key, &$userPermissionIds){
        //             if($k == $key) array_push($userPermissionIds, $v);
        //         });
        //         $builder =   $builder->whereIn('permission_id',$userPermissionIds);

        //     } else {
        //         $builder =   $builder->where('permission_id','=',$request->permission_id);
        //     }
        // }

        if (isset($request->Preparation)) {
            if( strpos($request->Preparation,',' ) !== false ) {
                
                $arrayPreps = explode(',',$request->Preparation);
                $builder =  $builder->whereIn('Preparation',$arrayPreps);
                                   
           } else if ($request->Preparation != 'All') {
                $builder =   $builder->where('Preparation','=',$request->Preparation);
            }
        }
        if (isset($request->Active)) {
            $builder =   $builder->where('Active','=',$request->Active);
        }
        try {
            $ips = $builder
            ->limit($request->limit)
            ->orderBy('id', 'asc')
            ->get($this->getRetrievedColumns())
            ->load(['permissions']);


            $gm_filtered_permission_array = [];
            if (isset($request->permission_id)) {
                if($request->permission_id == 'All'){
                     //filter by permission  
                    // get permissions of the authenticated user  
                    $user = auth()->user();
                    $userPermissions = $user->getPermissions();
                    $userPermissionIds = array();
                    $key = 'id';
                    array_walk_recursive($userPermissions, function($v, $k) use($key, &$userPermissionIds){
                        if($k == $key) array_push($userPermissionIds, $v);
                    }); 
                    // lopp to all goods and materials
                    foreach ($ips as $ip){
                        $theIP_Permissions = $ip->permissions;
                        //loop through all the assigened permission of the gm
                        foreach($theIP_Permissions as $theGM_Permission){
                            // if the permission of gm is one included in the user permissions
                            if(in_array($theGM_Permission->id,$userPermissionIds)) {
                                array_push($gm_filtered_permission_array,$ip);
                                break;
                            }
                        }
                    }
                } else {
                    // lopp to all goods and materials
                    foreach ($ips as $ip){
                        $theIP_Permissions = $ip->permissions;
                        //loop through all the assigened permission of the ip
                        foreach($theIP_Permissions as $theGM_Permission){
                            // if the permission of gm is the user permissions
                            if($theGM_Permission->id ==$request->permission_id) {
                                array_push($gm_filtered_permission_array,$ip);
                                break;
                            }
                        }
                    }
                }
            }

            // refresh gms with filtered permissions
            // if(!empty($gm_filtered_permission_array)){
                $ips = collect($gm_filtered_permission_array);
            // }
    

            return Intermediate_ProductResourceDB::collection($ips);         
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
        $c = Category::all('id','type','name')->whereIn('type',['All','I']);

        $returnArr = [];
        foreach ($c as  $sc) {
            $returnArr[$sc['id']] = $sc['name'];
        }
        return $returnArr;
    }
    public function getLocationOptions()
    {
        $c = Location::all('id','name');

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
    public function getPreparationOptions()
    {
        $returnArr = ['Yes'];
        return $returnArr;
    }   

}
