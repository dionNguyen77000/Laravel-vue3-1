<?php

namespace App\Http\Controllers\DataTable;

use App\Events\GoodMaterialEvent;
use Image;

use App\Mail\TestMail;
use App\Mail\MyTestMail;


use Barryvdh\DomPDF\PDF;
use App\Models\Permission;
use App\Models\Stock\Unit;
use Illuminate\Http\Request;
use App\Models\Stock\Category;
use App\Models\Stock\Location;
use App\Models\Stock\Supplier;
use Illuminate\Support\Carbon;
use Money\Parser\IntlMoneyParser;
use Illuminate\Support\Collection;
use Money\Currencies\ISOCurrencies;
use App\Http\Controllers\Controller;
use App\Models\Stock\Goods_material;
use Illuminate\Support\Facades\File;


use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
// use Maatwebsite\Excel\Excel;
use App\Exports\Goods_MaterialExport;
use App\Imports\Goods_MaterialImport;
use Illuminate\Support\Facades\Storage;
use App\Models\Stock\Orders_To_Supplier;
use App\Models\Stock\Invoices_From_Supplier;
use App\Models\Stock\Order_To_Supplier_Line;
use App\Http\Resources\Stock\Goods_MaterialResourceDB;
use App\Models\Stock\Intermediate_product;
use App\Models\Stock\Unit_Conversion;

class Goods_MaterialController extends DataTableController
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
          'created' => array_values($this->getCreatedColumns()),
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
        return Goods_material::query();
    }

    
    public function getCustomColumnsNames()
    {
        return [
            'unit_id' => 'Unit',
            // 'supplier_id' => 'Supplier',
            'category_id' => 'Category',
            'img_thumbnail'=>'image',
            'current_qty' => 'cur_qty',
            'prepared_point'=>'Ordered_Point',
            'location_id'=>'Location',
            'Preparation' => 'Prep',
            'required_qty'=> 'Required',
            'check_id'=> 'Check_Stock',
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id','name', 'img_thumbnail',
            // 'slug', 
            'price',
            'unit_id',
            // 'supplier_id',
            'category_id',
            'current_qty',
            'prepared_point',
            'coverage',
            'required_qty',
            'Preparation',
            'suppliers',
            'check_id',
            'permissions',
            'location_id',
            'O_Status',
            'Active',
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
            'price',
            'unit_id',
            // 'supplier_id',
            'category_id',
            'current_qty',
            'prepared_point',
            'coverage',
            'required_qty',
            'Preparation',
            'location_id',
            'check_id',
            'O_Status',
            'Active',
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            'name', 
            'img_thumbnail',
            // 'slug', 
            'price',
            'unit_id',
            // 'supplier_id',
            'category_id',
            'img',
            'current_qty',
            'prepared_point',
            'coverage',
            'required_qty',
            'Preparation',
            'suppliers',
            'location_id',
            'check_id',
            'permissions',
            'O_Status',
            'Active',
        ];
    }

    public function getCreatedColumns()
    {
        return [
            'name', 
            'img_thumbnail',
            // 'price',
            'unit_id',
            // 'supplier_id',
            'category_id',
            'img',
            'current_qty',
            'prepared_point',
            'coverage',
            'location_id',
            'check_id',
            // 'O_Status',
            'Active',
        ];
    }
    
    public function store(Request $request)
    {
      
        // $good_material = new Goods_material();
        // if($request->assignedSupplierIds && count($request->assignedSupplierIds) > 0 ) {
        //     // dd( $request->assignedSupplierIds);

        //     foreach($request->assignedSupplierUnitPrices as $assignedSupplierUnitPrice){
        //         // dd( $assignedSupplierUnitPrice);
        //         foreach($assignedSupplierUnitPrice as $supplierId => $unit_price){
        //             // var_dump($supplierId);
        //             dd($unit_price);
        //             $good_material->suppliers()->attach($supplierId,['unit_price'=>$unit_price]);
        //         }
        //     }
        // }

        $this->validate($request, [
            'name' => 'required|unique:goods_materials,name',
            // 'slug' => 'required|unique:goods_materials,slug',
            'price' => 'numeric|nullable',
            'current_qty' => 'required|numeric',
            'prepared_point' => 'required|numeric',
            'coverage' => 'required|numeric',
            'check_id' => 'required',
            'assignedSupplierIds' => 'required',
            'assignedPermissionIds' => 'required',
            'assignedSupplierUnitPrices' => 'required',
            // 'img' => 'image|mimes:jpeg,png,jpg,gif,svg'
        ]);
        $good_material = new Goods_material();
        $good_material->name = $request->name;
        $good_material->img_thumbnail = $request->img_thumbnail;
        $good_material->price = $request->price;
        $good_material->unit_id = $request->unit_id;
        $good_material->supplier_id = $request->supplier_id;
        $good_material->category_id = $request->category_id;
        $good_material->img = $request->img;
        $good_material->current_qty = $request->current_qty;
        $good_material->prepared_point = $request->prepared_point;
        $good_material->coverage = $request->coverage;
        $good_material->Active = $request->Active;
        $good_material->location_id = $request->location_id;
        $good_material->check_id = $request->check_id;
        

        // $good_material = $this->builder->create
        // (
        //     $request->only($this->getCreatedColumns())
        // );
        
        if ($good_material->current_qty <= $good_material->prepared_point){
            $good_material->Preparation = 'Yes';
            $good_material->required_qty = $good_material->coverage -   $good_material->current_qty;  
        } else{
            $good_material->Preparation = 'No';
            $good_material->required_qty = 0;
        }
        $good_material->save();
        if($request->assignedPermissionIds && count($request->assignedPermissionIds) > 0 ) {
            $good_material->permissions()->attach($request->assignedPermissionIds);
        }
        if($request->assignedSupplierIds && count($request->assignedSupplierIds) > 0 ) {
            foreach($request->assignedSupplierUnitPrices as $assignedSupplierUnitPrice){
                foreach($assignedSupplierUnitPrice as $supplierId => $unit_price){
                    // echo($supplierId );
                    $good_material->suppliers()->attach($supplierId,['unit_price'=>$unit_price]);
                }
            }
        }
        return $good_material;
       
    }
    public function updateCurrentQty($id,Request $request){
        $this->validate($request, [
            'current_qty' => 'required|numeric',
        ]);
        $GM =  $this->builder->find($id);
        $GM->current_qty = $request->current_qty;
          //update Preparation according to current_qty, Prepared_Point And Coverage
          if ($GM->current_qty <= $GM->prepared_point){
            $GM->Preparation = 'Yes';
            $GM->required_qty = $GM->coverage -   $GM->current_qty;  
           
        } else{
            $GM->Preparation = 'No';
            $GM->required_qty = 0;        
        } 
        if($GM->save()){
            broadcast(new GoodMaterialEvent())->toOthers();
        }       

        // dd($request->current_qty);
    }

    public function update($id, Request $request)
    {
        
        $this->validate($request, [
            'name' => 'required|unique:goods_materials,name,' . $id,
            'price' => 'numeric|nullable',
            'current_qty' => 'required|numeric',
            'prepared_point' => 'required|numeric',
            'coverage' => 'required|numeric',
            'check_id' => 'required',
            'assignedSupplierIds' => 'required',
            'assignedPermissionIds' => 'required',
            // 'suppliers' => 'required'

        ]);

        // $currencies = new ISOCurrencies();
        // $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        // $moneyParser = new IntlMoneyParser($numberFormatter, $currencies);
        // $money = $moneyParser->parse($request->input('price'));
        
        // dd ($money->getAmount()); // outputs 100

        $GM =  $this->builder->find($id);
        // dd($request);

        $GM->name = $request->name;
        $GM->price = $request->price;
        $GM->unit_id = $request->unit_id;
        $GM->supplier_id = $request->supplier_id;
        $GM->category_id = $request->category_id;
        $GM->current_qty = $request->current_qty;
        $GM->prepared_point = $request->prepared_point;
        $GM->coverage = $request->coverage;
        $GM->required_qty = $request->required_qty;
        $GM->Active = $request->Active;
        $GM->location_id = $request->location_id;
        $GM->check_id = $request->check_id;
        if($request->O_Status == ''){
            $GM->O_Status = null;
        }
        else $GM->O_Status = $request->O_Status;
        // $updatedSuccess = $GM->update(
        //     $request->only($this->getUpdatableColumns())
        // );
        //update the permissions of the good_material
        if($request->assignedPermissionIds && count($request->assignedPermissionIds) > 0 ) {
            $GM->permissions()->sync($request->assignedPermissionIds);
        }
        // if($request->assignedSupplierIds && count($request->assignedSupplierIds) > 0 ) {
        //     $GM->suppliers()->sync($request->assignedSupplierIds);
        // }
        // dd( $request->assignedSupplierUnitPrices);

        // $GM->suppliers()->sync([ 
        //     1 => ['unit_price' => 0],
        //     2 => ['unit_price' => 0.07],
        //     6 => ['unit_price' => 0.09],
        // ]);

        // dd($request->assignedSupplierUnitPrices);
        $syncedUnitPriceArray = [

        ];

        if($request->assignedSupplierIds && count($request->assignedSupplierIds) > 0 ) {
            foreach($request->assignedSupplierUnitPrices as $assignedSupplierUnitPrice){
                foreach($assignedSupplierUnitPrice as $supplierId => $unit_price){
                    $syncedUnitPriceArray[$supplierId] = ['unit_price'=>$unit_price];
                    // echo($supplierId );
                    // echo($unit_price );
                    // $GM->suppliers()->sync([
                    //     // $supplierId => ['unit_price'=>$unit_price]
                    // ]);
                }
            }
        }

        // dd($syncedUnitPriceArray);

        $GM->suppliers()->sync($syncedUnitPriceArray);

        // if($request->assignedSupplierUnitPrices && count($request->assignedSupplierUnitPrices) > 0 ) {
        //     foreach($request->assignedSupplierUnitPrices as $assignedSupplierUnitPrice){
            
        //             $GM
        //             ->suppliers()
        //             ->sync(
        //                 $assignedSupplierUnitPrice['id'],
        //                     ['unit_price'=>$assignedSupplierUnitPrice['pivot']['unit_price']]
        //                 );
        //     }
        // }

        //update Preparation according to current_qty, Prepared_Point And Coverage
        if ($GM->current_qty <= $GM->prepared_point){
            $GM->Preparation = 'Yes';
            $GM->required_qty = $GM->coverage -   $GM->current_qty;  
           
        } else{
            $GM->Preparation = 'No';
            $GM->required_qty = 0;        
        } 
        if( $GM->save()){

            broadcast(new GoodMaterialEvent())->toOthers();
        }
             
        

        return $GM;
    }

    public function saveLocationImage($id, Request $request)
    {
   
        $this->validate($request, [
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
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
        Storage::put("public/good_material_images/". $imageName, $imageNameResize->__toString());
        Storage::put("public/good_material_images/". $thumbnailName, $thumbnailNameResize->__toString());

        $the_good_material = $this->builder->find($id);

        if ($the_good_material->img_thumbnail){
            $result_thumbnail_array = explode('/',$the_good_material->img_thumbnail);
            $old_thumbnail_name = $result_thumbnail_array[count($result_thumbnail_array)-1];
            Storage::delete([
            "public/good_material_images/". $old_thumbnail_name,
            ]);
        }
        if ($the_good_material->img){
            $result_image_array = explode('/',$the_good_material->img);
            $old_image_name = $result_image_array[count($result_image_array)-1];
            Storage::delete([
            "public/good_material_images/". $old_image_name,
            ]);
        }
        // save new image
        $the_good_material -> img_thumbnail = "/storage/good_material_images/".$thumbnailName;
        $the_good_material -> img = "/storage/good_material_images/".$imageName;
        $the_good_material -> save();
        broadcast(new GoodMaterialEvent())->toOthers();



        // $img_thumbnail = $image->getClientOriginalName();
        // $img_thumbnail = time().'_thumbnail_'.$img_thumbnail;
        
        // // resize to make img_thumbnail image
        // Image::make($image)
        // ->fit(200,200)
        // ->save(public_path('/good_material_images/').$img_thumbnail);

        // calculate md5 hash of encoded image
        // $now = Carbon::now()->toDateTimeString();       
        // $hash = md5($image->__toString().$now);
        // $hash = md5($imageResize->__toString());

        // use hash as a name
        // $path = "good_material_images/{$hash}.jpg";

        // save it locally to ~/public/images/{$hash}.jpg
        // $imageResize->save(public_path($path));

        // $url = "/images/{$hash}.jpg"
        // $url = "/" . $path;


         //upload img_thumbnail image and resize 
        // ->save(public_path('/good_material_images/').$imageName);


    

        // //delete old image


        // File::delete([
        //     public_path($the_good_material->image),
        //     public_path($the_good_material->img_thumbnail)
        // ]);

        // $the_good_material -> image = '/good_material_images/' .  $imageName;
        // $the_good_material -> img_thumbnail = '/good_material_images/' .  $img_thumbnail;
        // // $the_good_material -> img_thumbnail = '/good_material_images/' .  $img_thumbnail;
        
        // $the_good_material -> save();
        return $the_good_material;
        // return "successfully saved";
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
        // Storage::put("public/good_material_images/". $imageName, $imageNameResize->__toString());
        // Storage::put("public/good_material_images/". $thumbnailName, $thumbnailNameResize->__toString());

        $dateInBrisbane= Carbon::now('Australia/Brisbane');
        $receivedDate=$dateInBrisbane->isoFormat('MMMDoYY'); 

        $the_g_m= $this->builder->find($silderImageidArray[0]);

        // deal with second or third invoice image
        if(count($silderImageidArray)>1){
            $img_number = $silderImageidArray[1];
            $imageName = $the_g_m->name.rand(1,999).'_'.$img_number.'.jpg';


            //delete old image
            if ($img_number == 2) {
                if ($the_g_m->img_two){
                    $result_image_array = explode('/',$the_g_m->img_two);
                    $old_image_name = $result_image_array[count($result_image_array)-1];
                    Storage::delete([
                    "public/good_material_images/". $old_image_name,
                    ]);
                }
                Storage::put("public/good_material_images/". $imageName, $imageNameResize->__toString());
                // save new image path to database
                $the_g_m -> img_two = "/storage/good_material_images/".$imageName;
                $the_g_m -> save();
            } else if ($img_number == 3) {
                if ($the_g_m->img_three){
                    $result_image_array = explode('/',$the_g_m->img_three);
                    $old_image_name = $result_image_array[count($result_image_array)-1];
                    Storage::delete([
                    "public/good_material_images/". $old_image_name,
                    ]);
                }
                Storage::put("public/good_material_images/". $imageName, $imageNameResize->__toString());
                $the_g_m -> img_three = "/storage/good_material_images/".$imageName;
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
                "public/good_material_images/". $old_thumbnail_name,
                ]);
            }
            if ($the_g_m->img){
                $result_image_array = explode('/',$the_g_m->img);
                $old_image_name = $result_image_array[count($result_image_array)-1];
                Storage::delete([
                "public/good_material_images/". $old_image_name,
                ]);
            }

            $randomNumber = rand(1,999);
            $imageName = $the_g_m->name.$randomNumber.'.jpg';
            $thumbnailName = $the_g_m->name.'_thumbnail_'.$randomNumber.'.jpg';

            Storage::put("public/good_material_images/". $imageName, $imageNameResize->__toString());
            Storage::put("public/good_material_images/". $thumbnailName, $thumbnailNameResize->__toString());

            // save new image
            $the_g_m -> img_thumbnail = "/storage/good_material_images/".$thumbnailName;
            $the_g_m -> img = "/storage/good_material_images/".$imageName;
            $the_g_m -> save();
        }
        broadcast(new GoodMaterialEvent())->toOthers();

        return $the_g_m;
    }


    // public function show(){
      
    //     $details = [
    //         'title' => 'Mail from Golden Lor Yarrabilba',
    //         'body' => 'This is for testing mail using gmail'
    //     ];

    //     Mail::to('anhduc.nguyen77000@gmail.com')->send(new MyTestMail($details));

    //     return ("Email is Sent.");
    // }
    public function show(){
        dd("I am in show().");
    }

    protected function getRecords(Request $request)
    {
        $builder = $this->builder;
        // $builder =   $builder->where('Active','=',1)->get();
        // dd($builder);

        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }

        if (isset($request->category_id) && $request->category_id != 'All') {
            $builder =   $builder->where('category_id','=',$request->category_id);
        }

        if (isset($request->location_id) && $request->location_id != 'All') {
            $builder =   $builder->where('location_id','=',$request->location_id);
        }              
        if (isset($request->check_id) && $request->check_id != 'All') {
            $builder =   $builder->where('check_id','=',$request->check_id);
        }              

        if (isset($request->goods_MaterialId)) {
            $builder =   $builder->where('id','=',$request->goods_MaterialId);
        }
        // if (isset($request->permission_id)) {
        //     if($request->permission_id == 'All'){
        //         $user = auth()->user();
        //         $userPermissions = $user->getPermissions();
        //         // $gmPermissions = $builder->permissions->map->only(['id', 'name']);
        //         // dd($gmPermissions);
        //         $userPermissionIds = array();
        //         $key = 'id';
        //         array_walk_recursive($userPermissions, function($v, $k) use($key, &$userPermissionIds){
        //             if($k == $key) array_push($userPermissionIds, $v);
        //         });
        //         // dd($userPermissionIds);
        //         $builder =   $builder->whereIn('permission_id',$userPermissionIds);

        //     } else {
        //         // dd($request->permission_id);
        //         $builder =   $builder->where('permission_id','=',$request->permission_id);
        //     }
        // }

        if (isset($request->Preparation)) {
            // dd($request->Preparation);
            if( strpos($request->Preparation,',' ) !== false ) {
                
                $arrayPreps = explode(',',$request->Preparation);
                // dd( $arrayPreps[0]);
                // dd( $arrayPreps[1]);
                $builder =  $builder->whereIn('Preparation',$arrayPreps);
                                   
           } else if ($request->Preparation != 'All') {
                $builder =   $builder->where('Preparation','=',$request->Preparation);
            }
        }

        if (isset($request->Active)&& $request->Active != 'All') {
            $builder =   $builder->where('Active','=',$request->Active);
        }

        // $gs =Goods_material::latest()->with(['user'])->paginate(20);

        try {
            $goods_materials = $builder
                            ->limit($request->limit)
                            ->orderBy('id', 'asc')
                            ->get($this->getRetrievedColumns())
                            ->load(['permissions','suppliers']);
                            // ->paginate(2);
           
            // $gmPermissions = $builder->permissions->map->only(['id', 'name']);
            // dd($gmPermissions);
            $gm_filtered_supplier_array = [];
            if (isset($request->supplier_id) && $request->supplier_id != 'All' ) {
                 // lopp to all goods and materials
                 foreach ($goods_materials as $good_material){
                    $theGM_Suppliers = $good_material->suppliers;
                    //loop through all the assigened permission of the gm
                    foreach($theGM_Suppliers as $theGM_Supplier){
                        // if the permission of gm is the user permissions
                        if($theGM_Supplier->id == $request->supplier_id) {
                            // dd($request->permission_id);

                            array_push($gm_filtered_supplier_array,$good_material);
                            break;
                        }
                    }
                }

                $goods_materials = collect($gm_filtered_supplier_array);
            }

            
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
                    foreach ($goods_materials as $good_material){
                        $theGM_Permissions = $good_material->permissions;
                        //loop through all the assigened permission of the gm
                        foreach($theGM_Permissions as $theGM_Permission){
                            // if the permission of gm is one included in the user permissions
                            if(in_array($theGM_Permission->id,$userPermissionIds)) {
                                array_push($gm_filtered_permission_array,$good_material);
                                break;
                            }
                        }
                    }
                } else {
                    // lopp to all goods and materials
                    foreach ($goods_materials as $good_material){
                        $theGM_Permissions = $good_material->permissions;
                        //loop through all the assigened permission of the gm
                        foreach($theGM_Permissions as $theGM_Permission){
                            // if the permission of gm is the user permissions
                            if($theGM_Permission->id == $request->permission_id) {
                                // dd($request->permission_id);

                                array_push($gm_filtered_permission_array,$good_material);
                                break;
                            }
                        }
                    }
                }
            }

            // refresh gms with filtered permissions
            // if(!empty($gm_filtered_permission_array)){
                $goods_materials = collect($gm_filtered_permission_array);
            // }


            $pr =  Goods_MaterialResourceDB::collection(
                $goods_materials
                // ->get($this->getDisplayableColumns())
            );
            // dd($pr);
            // return $ $goods_materials_builder;
            return $pr;
            
          
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
        $c = Category::all('id','type','name')->whereIn('type',['All','GM']);

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

     /**
    * @return \Illuminate\Support\Collection
    */
    public function fileImportExport()
    {
    //    return view('import');
       return 'hello';
    }
     
    /**
    * @return \Illuminate\Support\Collection
    */
    public function fileExport1($supplier_id,Request $request) 
    {
        // get array data from request
        $data = json_decode($request->getContent(), true);
        // dd($data);
        
        $supplier = Supplier::find($supplier_id);
        $supplierName=$supplier->name;
        $dateInBrisbane= Carbon::now('Australia/Brisbane');
        $orderDate=$dateInBrisbane->isoFormat('MMMDoYY'); 
        $currentYear = $dateInBrisbane->format('Y');
        $currentMonth = $dateInBrisbane->format('M');

        $fileName="Order$supplierName"."_"."$orderDate";

        $pathXLSX = "public/orders/$currentYear/$currentMonth/$fileName.xlsx";
        // $pathCSV = "public/orders/$currentYear/$currentMonth/$fileName.csv";
        // $pathPDF = "public/orders/$currentYear/$currentMonth/$fileName.pdf";
        // return (new Goods_MaterialExport)->download('goods.xlsx',\Maatwebsite\Excel\Excel::CSV,
        // ['Content-Type' => 'text/csv']
        // );
        // return (new Goods_MaterialExport)->download('goods.xlsx',\Maatwebsite\Excel\Excel::XLSX);
        // return (new Goods_MaterialExport)->download('goods.xlsx',\Maatwebsite\Excel\Excel::XLSX);
        // return (new Goods_MaterialExport)->download('goods.csv',\Maatwebsite\Excel\Excel::CSV);
        // return (new Goods_MaterialExport)->download('goods.csv', \Maatwebsite\Excel\Excel::CSV, [
        //     'Content-Type' => 'text/csv',
        // ]);

        


        $sucessfullXLSXGeneration  = (new Goods_MaterialExport($supplier_id, $data))->store($pathXLSX);       
        // $sucessfullXLSXGeneration  = (new Goods_MaterialExport($supplier_id))->store($pathXLSX);       
        // (new Goods_MaterialExport($supplier_id))->store($pathCSV );       
        // (new Goods_MaterialExport($supplier_id))->store($pathPDF );    
        // return (new Goods_MaterialExport($supplier_id))->download('goods.xlsx');  
        return (new Goods_MaterialExport($supplier_id, $data))->download('goods.xlsx');  
        
    
        // return (new Goods_MaterialExport)->store($pathPDF, \Barryvdh\DomPDF\PDF::DOMPDF);        
  
        // return (new Goods_MaterialExport)->store($pathXLSX);
        // return Excel::store(new Goods_MaterialExport(2018), $pathCSV,\Maatwebsite\Excel\Excel::CSV);
        // return Excel::store(new Goods_MaterialExport(2018), $pathXLSX);
       
        // return Excel::download(new Goods_MaterialExport, 'goods.xlsx');
    }
     
    /**
    * @return \Illuminate\Support\Collection
    */
    public function fileImport(Request $request) 
    {
        Excel::import(new Goods_MaterialImport,request()->file('file'));
             
        return back();
    }

    public function getSupplierDetails($supplier_id,Request $request) 
    {

        $supplier = Supplier::find($supplier_id);
        $supplierName=$supplier->name;
        $dateInBrisbane= Carbon::now('Australia/Brisbane');
        $orderDate=$dateInBrisbane->isoFormat('MMMDoYY'); 
        $currentYear = $dateInBrisbane->format('Y');
        $currentMonth = $dateInBrisbane->format('M');

        $fileName="Order$supplierName"."_"."$orderDate";

        $pathXLSX = "public/orders/$currentYear/$currentMonth/$fileName.xlsx";

        $fileName="Order$supplierName"."_"."$orderDate";

        $pathXLSX = "public/orders/$currentYear/$currentMonth/$fileName.xlsx";


        return $supplier;
    }
    public function checkExcelFileIfGenerated($supplier_id,Request $request) 
    {
        $supplier = Supplier::find($supplier_id);
        $supplierName=$supplier->name;
        $dateInBrisbane= Carbon::now('Australia/Brisbane');
        $orderDate=$dateInBrisbane->isoFormat('MMMDoYY'); 
        $currentYear = $dateInBrisbane->format('Y');
        $currentMonth = $dateInBrisbane->format('M');

        $fileName="Order$supplierName"."_"."$orderDate";

        $pathXLSX = "public/orders/$currentYear/$currentMonth/$fileName.xlsx";

        $fileName="Order$supplierName"."_"."$orderDate";

        $pathXLSX = "public/orders/$currentYear/$currentMonth/$fileName.xlsx";

        $file=public_path("storage/orders/$currentYear/$currentMonth/$fileName.xlsx");
        $fileExit =  file_exists($file);

        // $fileExit =  file_exists($pathXLSX);

        if ($fileExit) {
            return "/storage/orders/$currentYear/$currentMonth/$fileName.xlsx";
        } else {
            return false;
        }
    }

    public function emailOrderToSupplier($supplier_id, Request $request){
        // // dd(storage_path('img/logo_backend.png'));
        // dd($emailDetail);
        // $excelData = $request->excelData;
        $emailDetail = $request->supplierInfo;
        // dd($emailDetail['email']);
        $details = [
            'email' => $emailDetail['email'],
            'title' => 'Order From Golden Lor Yarrabilba',
            'body' =>  $emailDetail['optionalMessage']
        ];

        $files = [
            public_path($emailDetail['excelFileName'])
        ];
        try{ 
            Mail::send('emails.myTestMail', $details, function($message)use($details, $files) {
                $message->to($details["email"], $details["email"])
                        ->subject($details["title"]);

                foreach ($files as $file){
                    $message->attach($file);
                }   
                // dd(Mail::failures());
                // echo ("Email is Sent successfully to supplier");    
               
            });
        // dd(Mail::failures());
        //     if(Mail::failures()) {
        //         echo "There was one or more failures. They were: <br />";
             
        //         foreach(Mail::failures() as $email_address) {
        //             echo " - $email_address <br />";
        //          }
             
        //      } else {
        //          return "No errors, all sent successfully!";
        //      }
        }
        catch(\Exception $e){
            // echo $e->getMessage();
            echo ('cannot send email');
        }
        // Mail::to('anhduc.nguyen77000@gmail.com')->send(new MyTestMail($details));
        if(count(Mail::failures())>0) {
            foreach(Mail::failures() as $email_address) {
                    echo " - $email_address <br />";
                }
            
            } 
        else {
            return "Email Order Successfully";
        }
    //    return $mail_status;
    }

    
    public function createOrderToSupplier($supplier_id, Request $request){

        $excelData = $request->excelData;
        $emailDetail = $request->supplierInfo;

        // dd($excelData);

        $user = auth()->user();
        //create new order and insert into table ordertosupplier and ordertosupplierline            
        //create new ordertosupplier order
        $theDate= Carbon::now();
        $supplier = Supplier::find($supplier_id);

        // create new order to supplier
        $order_to_supplier = new Orders_To_Supplier();
        $order_to_supplier->user =$user->name;
        $order_to_supplier->supplier = $supplier->name;
        $order_to_supplier->ordered_date =$theDate;
        $order_to_supplier->excel_file =$emailDetail['excelFileName'];
        $order_to_supplier->save();
        
        
        // create new invoice from supplier
        $invoice_from_supplier = new Invoices_From_Supplier();
        $invoice_from_supplier ->orders_to_supplier_id = $order_to_supplier->id;
        $invoice_from_supplier ->supplier = $order_to_supplier->supplier;
        $invoice_from_supplier ->paid ='No';
        $invoice_from_supplier->save();
        

        // $good_materials = Goods_material::where('supplier_id', '=', $supplier_id)
        // ->where('required_qty','>',0)
        // ->orderBy('category_id','asc')
        // ->get();


        $estimated_price_order_to_supplier = 0;
        foreach($excelData as $good_material)
        {
            $theGM = Goods_material::where('name', '=',  $good_material['name'])->first();
            // dd($theGM->Order_Status);
            $theGM->O_Status = 'waiting';
            $theGM->save();

            $order_to_supplier_line = new Order_To_Supplier_Line();
            $order_to_supplier_line->orders_to_supplier_id = $order_to_supplier->id;
            // $order_to_supplier_line->goods_material_id = $good_material->id;
            //update order_status of the good_material in stock          

            $order_to_supplier_line->goods_material = $good_material['name'];
            $order_to_supplier_line->o_unit_price = $good_material['price'];

            $theUnit = Unit::find($good_material['unit_id']);

            $order_to_supplier_line->o_unit = $theUnit->name;
            
            $theCategory = Category::find($good_material['category_id']);

            $order_to_supplier_line->category = $theCategory->name;

            $order_to_supplier_line->o_unit_quantity = $good_material['required_qty'];
            $order_to_supplier_line->o_line_price = $good_material['price'] * $good_material['required_qty'];
            
            $order_to_supplier_line->i_unit = $theUnit->name;
            $order_to_supplier_line->i_unit_quantity = $good_material['required_qty'];
            $order_to_supplier_line->i_unit_price = $good_material['price'];
            $order_to_supplier_line->i_line_price = $good_material['price'] * $good_material['required_qty'];

            $order_to_supplier_line -> save();

            $estimated_price_order_to_supplier = $estimated_price_order_to_supplier + 
            $good_material['price']*$good_material['required_qty'];

          
        }
        $order_to_supplier->estimated_price = $estimated_price_order_to_supplier;

        $order_to_supplier->save();

        return $order_to_supplier;

    }

    public function orderAndEmailSupplier($supplier_id, Request $request){

        $excelData = $request->excelData;
        $emailDetail = $request->supplierInfo;
        // dd($emailDetail);
        // $this->validate($request->supplierInfo, [       
        //     'email' => 'required|email',
        // ]);

        if ($this->emailOrderToSupplier($supplier_id, $request) == 'Email Order Successfully') {
            return $this->createOrderToSupplier($supplier_id,  $request);
        }
    }

    public function removeWaitingStatus($ids){
        $arrayIds = explode(',',$ids);

        if (count($arrayIds) > 0 ) {
            foreach($arrayIds as $id){
                $theGM = Goods_material::find($id);
                $theGM->O_Status = null;
                $theGM->save();
            }
        }
      

    }
    public function updateCurrentQtyFromIntemediate($ids){
        $arrayIds = explode(',',$ids);
        
        if (count($arrayIds) > 0 ) {

            $totalChickenMinceRequiredForWonton = 0;
            $totalChickenMinceRequiredForDimsim = 0;
            $totalChickenMinceRequiredForCurryPuff = 0;
            $totalChickenMinceRequiredForDimsim = 0;
            $totalChickenRequiredForHoneyChicken = 0;
            $totalPorkMinceRequiredForSpringRoll = 0;
            $totalPorkRequiredForBBQPork = 0;
            $totalPorkRequiredForSSPork = 0;
            $totalChickenSlices = 0;
            $totalBeefSlices = 0;
            $totalLambSlices = 0;
            $order2kgOr4kg=0;
            // for each GM 
            foreach($arrayIds as $id){
                // get all unit conversions of the gm 
                $theGM = Goods_material::find($id);

                //get all the unit conversions for $theGM - the GM may have more than 1 unit conversion
                $theUnitConversions = Unit_Conversion::where('goods_material_id',$id)->get();
            //    dd(count($theUnitConversions));
                // if the GM has to convert unit from intermediate product
                if(count($theUnitConversions)>0) {   
                    // dd($theUnitConversion);
                    $newCurrentQty = 0;
                    $newPreparedPoint = 0;
                    $newCoverage = 0;
                    $newRequiredQty = 0;
                    foreach ($theUnitConversions as $theUnitConversion) {
                        $theIP = Intermediate_product::find($theUnitConversion->intermediate_product_id);
                        // dd($theIP);
                        // if($theIP->required_qty > 0){
                            $newCurrentQty = $newCurrentQty + $theIP->current_qty * $theUnitConversion->rate;
                            $newPreparedPoint =  $newPreparedPoint + $theIP->prepared_point * $theUnitConversion->rate;
                            $newCoverage=  $newCoverage+ $theIP->coverage * $theUnitConversion->rate;
                            $newRequiredQty=  $newRequiredQty+ $theIP->required_qty * $theUnitConversion->rate;
                        // }
                    } 
                    // dd($newCurrentQty);
                // if there is at least one IP have required_qty which is greater than 0
                    // if($newRequiredQty > 0){   
                        $theGM->current_qty = $newCurrentQty;
                        $theGM->prepared_point = $newPreparedPoint;
                        $theGM->coverage = $newCoverage;         
                        $theGM->required_qty = $newRequiredQty;  
                
                        //update Preparation according to current_qty, Prepared_Point And Coverage
                        if ($theGM->current_qty <= $theGM->prepared_point){
                            $theGM->Preparation = 'Yes';
                        } else {
                            $theGM->Preparation = 'No';
                        }
                        if($theGM->save()){
                            broadcast(new GoodMaterialEvent())->toOthers();
                        }    
                        //total kg of chicken mince for wonton need to order (Deep Fry + Steam)
                        if($theGM->name == 'Chicken Mince (Wonton)'){
                            $totalChickenMinceRequiredForWonton = $totalChickenMinceRequiredForWonton + $theGM->required_qty;
                        }
                        //total kg of chicken mince for curry_puff need to order (Filling + Spring Roll)
                        if($theGM->name == 'Chicken Mince (Curry_Puff)'){
                            $totalChickenMinceRequiredForCurryPuff = $totalChickenMinceRequiredForCurryPuff + $theGM->required_qty;
                        }
                        //total kg ofchicken mince for dimsim need to order
                        if($theGM->name == 'Pork Mince (Spring_Roll)'){
                            $totalPorkMinceRequiredForSpringRoll = $totalPorkMinceRequiredForSpringRoll + $theGM->required_qty;
                        }
                        //total kg of pork mince for spring roll need to order
                        if($theGM->name == 'Chicken Mince (Dimsim)'){
                            $totalChickenMinceRequiredForDimsim = $totalChickenMinceRequiredForDimsim + $theGM->required_qty;
                        }
                        
                        if($theGM->name == 'Pork (BBQ Pork)'){
                            $totalPorkRequiredForBBQPork = $totalPorkRequiredForBBQPork + $theGM->required_qty;
                        }
                        
                        if($theGM->name == 'Pork (S&S_Pork)'){
                            $totalPorkRequiredForSSPork = $totalPorkRequiredForSSPork + $theGM->required_qty;
                        }
                        if($theGM->name == 'Chicken Breast (Honey Chicken)'){
                            $totalChickenRequiredForHoneyChicken = $totalChickenRequiredForHoneyChicken + $theGM->required_qty;
                        }
                        if($theGM->name == 'Chicken (Breast Slice)'){
                            $totalChickenSlices = $totalChickenSlices + $theGM->required_qty;
                        }
                        if($theGM->name == 'Beef (Topside Slices)'){
                            $totalBeefSlices = $totalBeefSlices + $theGM->required_qty;
                        }
                        if($theGM->name == 'Lamb Slices'){
                            $totalLambSlices = $totalLambSlices + $theGM->required_qty;
                        }
                            
                        // } else{
                        //     $theGM->Preparation = 'No';
                        //     $theGM->required_qty = 0;        
                        // } 
                       
                    // }
                   
                }
            }

            // update required qty of wonton in the rule of 2kg, 4kg for 'Chicken Mince (Wonton_Deep_Fry)' and 'Chicken Mince (Wonton_Steam)'                                                                                                                                                                                                                    4kg
            // if($totalChickenMinceRequiredForWonton > 0.8 && $totalChickenMinceRequiredForWonton < 2.5){
            //     $order2kgOr4kg = 2;
            //     $theGM_Wonton_DeepFry = Goods_material::where('name','Chicken Mince (Wonton_Deep_Fry)')->first();
            //     $theGM_Wonton_Steam = Goods_material::where('name','Chicken Mince (Wonton_Steam)')->first();
            //     if($theGM_Wonton_DeepFry->required_qty > 0 && $theGM_Wonton_Steam->required_qty > 0){
            //         $ratioOfDFAndS = $theGM_Wonton_DeepFry->required_qty/$theGM_Wonton_Steam->required_qty;
            //         $theGM_Wonton_Steam->required_qty =  round((2/(1+$ratioOfDFAndS)),2);
            //         $theGM_Wonton_DeepFry->required_qty = round((2 -  $theGM_Wonton_Steam->required_qty),2);
            //         // dd($theGM_Wonton_Steam->required_qty);
            //         $theGM_Wonton_DeepFry->save();
            //         $theGM_Wonton_Steam->save();
            //     } else if ($theGM_Wonton_DeepFry->required_qty == 0) {
            //         $theGM_Wonton_Steam->required_qty = 2;
            //         $theGM_Wonton_Steam->save();
            //     }   else if ( $theGM_Wonton_Steam->required_qty == 0){
            //         $theGM_Wonton_DeepFry->required_qty =  2;
            //         $theGM_Wonton_DeepFry->save();
            //     }    
            // // update required qty in the rule of 4kg /    
            // } elseif ($totalChickenMinceRequiredForWonton >= 2.5) {
            //     $order2kgOr4kg = 4;
            //     $theGM_Wonton_DeepFry = Goods_material::where('name','Chicken Mince (Wonton_Deep_Fry)')->first();
            //     $theGM_Wonton_Steam = Goods_material::where('name','Chicken Mince (Wonton_Steam)')->first();
            //     if($theGM_Wonton_DeepFry->required_qty != 0 && $theGM_Wonton_Steam->required_qty != 0){
            //         $ratioOfDFAndS = $theGM_Wonton_DeepFry->required_qty/$theGM_Wonton_Steam->required_qty;
            //         $theGM_Wonton_Steam->required_qty =  round((4/(1+$ratioOfDFAndS)),2);
            //         $theGM_Wonton_DeepFry->required_qty = round((4 -  $theGM_Wonton_Steam->required_qty),2);
            //         $theGM_Wonton_DeepFry->save();
            //         $theGM_Wonton_Steam->save();
            //     } else if ($theGM_Wonton_DeepFry->required_qty == 0) {
            //         $theGM_Wonton_Steam->required_qty = 4;
            //         $theGM_Wonton_Steam->save();
            //     }   else if ( $theGM_Wonton_Steam->required_qty == 0){
            //         $theGM_Wonton_DeepFry->required_qty =  4;
            //         $theGM_Wonton_DeepFry->save();
            //     }    
            // }

            // update required qty of wonton in the rule of 2,4                                                                                                                                                                                                                      4kg
            if($totalChickenMinceRequiredForWonton > 0.5 && $totalChickenMinceRequiredForWonton < 2.5){
                $theGM_CurryPuff = Goods_material::where('name','Chicken Mince (Wonton)')->first();
                $theGM_CurryPuff->required_qty = 2;
                $theGM_CurryPuff->save();
            } elseif ($totalChickenMinceRequiredForWonton >= 2.5){
                $theGM_CurryPuff = Goods_material::where('name','Chicken Mince (Wonton)')->first();
                $theGM_CurryPuff->required_qty = 4;
                $theGM_CurryPuff->save();
            }
            // update required qty of currypuff in the rule of 2kg                                                                                                                                                                                                                      4kg
            if($totalChickenMinceRequiredForCurryPuff > 0.8 && $totalChickenMinceRequiredForCurryPuff < 3){
                $theGM_CurryPuff = Goods_material::where('name','Chicken Mince (Curry_Puff)')->first();
                $theGM_CurryPuff->required_qty = 2;
                $theGM_CurryPuff->save();
            } elseif ($totalChickenMinceRequiredForCurryPuff >= 3){
                $theGM_CurryPuff = Goods_material::where('name','Chicken Mince (Curry_Puff)')->first();
                $theGM_CurryPuff->required_qty = 4;
                $theGM_CurryPuff->save();
            }

            // update required qty of dimsim in the rule of 2kg                                                                                                                                                                                                                      4kg
            if($totalChickenMinceRequiredForDimsim > 0.8 && $totalChickenMinceRequiredForDimsim < 3){
                $theGM_Dimsim = Goods_material::where('name','Chicken Mince (Dimsim)')->first();
                $theGM_Dimsim->required_qty = 2;
                $theGM_Dimsim->save();
            } elseif ($totalChickenMinceRequiredForDimsim >= 3){
                $theGM_Dimsim = Goods_material::where('name','Chicken Mince (Dimsim)')->first();
                $theGM_Dimsim->required_qty = 4;
                $theGM_Dimsim->save();
            }
            
            // update required qty of porkmince in the rule of 2kg, 4kg and 6kg                                                                                                                                                                                                                    4kg
            if($totalPorkMinceRequiredForSpringRoll > 0.8 && $totalPorkMinceRequiredForSpringRoll < 3){
                $theGM_SpringRoll = Goods_material::where('name','Pork Mince (Spring_Roll)')->first();
                $theGM_SpringRoll->required_qty = 2;
                $theGM_SpringRoll->save();
            } elseif ($totalPorkMinceRequiredForSpringRoll >= 3 && $totalPorkMinceRequiredForSpringRoll < 6){
                $theGM_SpringRoll = Goods_material::where('name','Pork Mince (Spring_Roll)')->first();
                $theGM_SpringRoll->required_qty = 4;
                $theGM_SpringRoll->save();
            
            } elseif ($totalPorkMinceRequiredForSpringRoll >= 6){
                $theGM_SpringRoll = Goods_material::where('name','Pork Mince (Spring_Roll)')->first();
                $theGM_SpringRoll->required_qty = 6;
                $theGM_SpringRoll->save();
            }
            // update required qty of Pork for BBQ in the rule of 10kg, 15kg, 20kg and 25kg                                                                                                                                                                                                                    4kg
            if($totalPorkRequiredForSSPork > 5 && $totalPorkRequiredForSSPork <10 ){
                $theGM_SSPork = Goods_material::where('name','Pork (S&S_Pork)')->first();
                $theGM_SSPork->required_qty = 10;
                $theGM_SSPork->save();
            } elseif ($totalPorkRequiredForSSPork >= 10 && $totalPorkRequiredForSSPork < 15){
                $theGM_SSPork = Goods_material::where('name','Pork (S&S_Pork)')->first();
                $theGM_SSPork->required_qty = 15;
                $theGM_SSPork->save();
            
            } elseif ($totalPorkRequiredForSSPork >= 15){
                $theGM_SSPork = Goods_material::where('name','Pork (S&S_Pork)')->first();
                $theGM_SSPork->required_qty = 20;
                $theGM_SSPork->save();
            
            } elseif ($totalPorkRequiredForSSPork >= 20){
                $theGM_SSPork = Goods_material::where('name','Pork (S&S_Pork)')->first();
                $theGM_SSPork->required_qty = 25;
                $theGM_SSPork->save();
            }
            // update required qty of Pork for BBQ in the rule of 2kg, 4kg and 6kg                                                                                                                                                                                                                    4kg
            if($totalPorkRequiredForBBQPork > 5 && $totalPorkRequiredForBBQPork <10 ){
                $theGM_BBQPork = Goods_material::where('name','Pork (BBQ Pork)')->first();
                $theGM_BBQPork->required_qty = 10;
                $theGM_BBQPork->save();
            } elseif ($totalPorkRequiredForBBQPork >= 10 && $totalPorkRequiredForBBQPork < 15){
                $theGM_BBQPork = Goods_material::where('name','Pork (BBQ Pork)')->first();
                $theGM_BBQPork->required_qty = 15;
                $theGM_BBQPork->save();
            
            } elseif ($totalPorkRequiredForBBQPork >= 15){
                $theGM_BBQPork = Goods_material::where('name','Pork (BBQ Pork)')->first();
                $theGM_BBQPork->required_qty = 20;
                $theGM_BBQPork->save();
            }

            // update required qty of Chicken for Honey CHicken in the rule of 10kg,15kg, 20kg and 25kg                                                                                                                                                                                                                    4kg
            if($totalChickenRequiredForHoneyChicken > 5 && $totalChickenRequiredForHoneyChicken <10 ){
                $theGM_BBQPork = Goods_material::where('name','Pork (BBQ Pork)')->first();
                $theGM_BBQPork->required_qty = 10;
                $theGM_BBQPork->save();
            } elseif ($totalChickenRequiredForHoneyChicken >= 10 && $totalChickenRequiredForHoneyChicken < 15){
                $theGM_BBQPork = Goods_material::where('name','Pork (BBQ Pork)')->first();
                $theGM_BBQPork->required_qty = 15;
                $theGM_BBQPork->save();
            
            } elseif ($totalChickenRequiredForHoneyChicken >= 15){
                $theGM_BBQPork = Goods_material::where('name','Pork (BBQ Pork)')->first();
                $theGM_BBQPork->required_qty = 20;
                $theGM_BBQPork->save();
            }

            // update required qty of Chicken/Beef/Lamb slices in the rule of 10kg,15kg, 20kg, 25kg,30kg,35 and 40kg                                                                                                                                                                                                                    4kg
            if($totalChickenSlices > 5 && $totalChickenSlices <10 ){
                $theGM_ChickenSlices = Goods_material::where('name','Chicken (Breast Slice)')->first();
                $theGM_ChickenSlices->required_qty = 10;
                $theGM_ChickenSlices->save();
            } elseif ($totalChickenSlices >= 10 && $totalChickenSlices < 15){
                $theGM_ChickenSlices = Goods_material::where('name','Chicken (Breast Slice)')->first();
                $theGM_ChickenSlices->required_qty = 15;
                $theGM_ChickenSlices->save();
            
            } elseif ($totalChickenSlices >= 15 && $totalChickenSlices < 20){
                $theGM_ChickenSlices = Goods_material::where('name','Chicken (Breast Slice)')->first();
                $theGM_ChickenSlices->required_qty = 20;
                $theGM_ChickenSlices->save();
            
            } elseif ($totalChickenSlices >= 20 && $totalChickenSlices < 25){
                $theGM_ChickenSlices = Goods_material::where('name','Chicken (Breast Slice)')->first();
                $theGM_ChickenSlices->required_qty = 25;
                $theGM_ChickenSlices->save();
            
            } elseif ($totalChickenSlices >= 25 && $totalChickenSlices < 30){
                $theGM_ChickenSlices = Goods_material::where('name','Chicken (Breast Slice)')->first();
                $theGM_ChickenSlices->required_qty = 30;
                $theGM_ChickenSlices->save();
            
            } elseif ($totalChickenSlices >= 30 && $totalChickenSlices < 35){
                $theGM_ChickenSlices = Goods_material::where('name','Chicken (Breast Slice)')->first();
                $theGM_ChickenSlices->required_qty = 35;
                $theGM_ChickenSlices->save();
            
            } elseif ($totalChickenSlices > 35 ){
                $theGM_ChickenSlices = Goods_material::where('name','Chicken (Breast Slice)')->first();
                $theGM_ChickenSlices->required_qty = 40;
                $theGM_ChickenSlices->save();
            }
            // update required qty of Beef for Stir Fry slices in the rule of 10kg,15kg, 20kg, 25kg,30kg,35 and 40kg                                                                                                                                                                                                                    4kg
            if($totalBeefSlices > 5 && $totalBeefSlices <10 ){
                $theGM_BeefSlices = Goods_material::where('name','Beef (Topside Slices)')->first();
                $theGM_BeefSlices->required_qty = 10;
                $theGM_BeefSlices->save();
            } elseif ($totalBeefSlices >= 10 && $totalBeefSlices < 15){
                $theGM_BeefSlices = Goods_material::where('name','Beef (Topside Slices)')->first();
                $theGM_BeefSlices->required_qty = 15;
                $theGM_BeefSlices->save();
            
            } elseif ($totalBeefSlices >= 15 && $totalBeefSlices < 20){
                $theGM_BeefSlices = Goods_material::where('name','Beef (Topside Slices)')->first();
                $theGM_BeefSlices->required_qty = 20;
                $theGM_BeefSlices->save();
            
            } elseif ($totalBeefSlices >= 20 && $totalBeefSlices < 25){
                $theGM_BeefSlices = Goods_material::where('name','Beef (Topside Slices)')->first();
                $theGM_BeefSlices->required_qty = 25;
                $theGM_BeefSlices->save();
            
            } elseif ($totalBeefSlices >= 25 && $totalBeefSlices < 30){
                $theGM_BeefSlices = Goods_material::where('name','Beef (Topside Slices)')->first();
                $theGM_BeefSlices->required_qty = 30;
                $theGM_BeefSlices->save();
            
            } elseif ($totalBeefSlices >= 30 && $totalBeefSlices < 35){
                $theGM_BeefSlices = Goods_material::where('name','Beef (Topside Slices)')->first();
                $theGM_BeefSlices->required_qty = 35;
                $theGM_BeefSlices->save();
            
            } elseif ($totalBeefSlices > 35 ){
                $theGM_BeefSlices = Goods_material::where('name','Beef (Topside Slices)')->first();
                $theGM_BeefSlices->required_qty = 40;
                $theGM_BeefSlices->save();
            }
            // update required qty of Lamb for Stir Fry slices in the rule of 5kg, 10kg,15kg, 20kg                                                                                                                                                                                                                 4kg
            if($totalLambSlices > 1 && $totalLambSlices <5 ){
                $theGM_LambSlices = Goods_material::where('name','Lamb Slices')->first();
                $theGM_LambSlices->required_qty = 5;
                $theGM_LambSlices->save();
            } 
            elseif ($totalLambSlices >= 5 && $totalLambSlices < 10){
                $theGM_LambSlices = Goods_material::where('name','Lamb Slices')->first();
                $theGM_LambSlices->required_qty = 10;
                $theGM_LambSlices->save();
            
            }elseif ($totalLambSlices >= 10 && $totalLambSlices < 15){
                $theGM_LambSlices = Goods_material::where('name','Lamb Slices')->first();
                $theGM_LambSlices->required_qty = 15;
                $theGM_LambSlices->save();
            
            } elseif ($totalLambSlices >= 15){
                $theGM_LambSlices = Goods_material::where('name','Lamb Slices')->first();
                $theGM_LambSlices->required_qty = 20;
                $theGM_LambSlices->save();           
            }  
            // var_dump('wonton total is: ' . $totalChickenMinceRequiredForWonton);
            // var_dump('currypuff total is: ' . $totalChickenMinceRequiredForCurryPuff);
            // var_dump('springRoll total is: ' . $totalPorkMinceRequiredForSpringRoll);
            // var_dump('springRoll total is: ' . $totalChickenMinceRequiredForDimsim);
        }
      

    }

    

}
