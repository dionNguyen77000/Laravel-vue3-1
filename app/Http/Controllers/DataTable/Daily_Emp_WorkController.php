<?php

namespace App\Http\Controllers\DataTable;

use Image;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Events\DailyEmpWorkEvent;
use App\Http\Controllers\Controller;
use App\Models\Stock\Daily_Emp_Work;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Stock\Intermediate_product;
use App\Http\Resources\Stock\Daily_Emp_WorkResourceDB;
use App\Http\Resources\Stock\Intermediate_ProductResourceDB;

class Daily_Emp_WorkController extends DataTableController
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
            'intermediate_ProductOptions'=> $this->getIntermediate_ProductOptions(),
            'needPrepareIntermediate_ProductOptions'=> $this->getNeedPrepareIntermediate_ProductOptions(),
            'needPrepareIntermediate_Products'=> $this->getNeedPrepareIntermediate_Products(),
            'permissionOptions'=> $this->getPermissionOptions(),
            'userPermissionOptions'=> $this->getUserPermissionOptions(),
            'authenticatedUser' =>$this->getAuthenticateduser(),
            'userRoleOptions'=> $this->getUserRoleOptions(),
            'userOptions'=> $this->getUserOptions(),
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
        return Daily_Emp_Work::query();
    }

    
    public function getCustomColumnsNames()
    {
        return [
            'user_id' => 'Staff',
            'intermediate_product_id' => 'Pre_Product',
            'current_prepared_qty' => 'Current',
            'required_qty' => 'Required',
            'done_qty' => 'Done'
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'date',
            'user_id',
            'intermediate_product_id',
            'done_qty',
            'current_prepared_qty',
            'required_qty',
            // 'role_id',
            'Status',
            'Note',
            'permission',
        ];
    }
    public function getRetrievedColumns()
    {
        return [
            'date',
            'user_id',
            'intermediate_product_id',
            'done_qty',
            'current_prepared_qty',
            'required_qty',
            // 'role_id',
            'Status',
            'Note',
            // 'permission',
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            'date',
            'user_id',
            'intermediate_product_id',
            'done_qty',
            'current_prepared_qty',
            'required_qty',
            // 'role_id',
            'Status',
            'Note'
        ];
    }

    public function getCreatedColumns()
    {
        return [
            'date',
            'user_id',
            'intermediate_product_id',
            'done_qty',
            'current_prepared_qty',
            'required_qty',
            'Status',
            'Note'
        ];
    }
    
    public function store(Request $request)

    {
        //  dd($request->done_qty);
        $this->validate($request, [
            'done_qty' => 'required|numeric',
            'current_prepared_qty' => 'required|numeric',
            'intermediate_product_id' => ['required', Rule::unique('daily_emp_works')->where(function ($query) use ($request) {
                return $query->where('date', $request->date)
                   ->where('user_id', $request->user_id);
             })]
            // 'date' => 'required|unique:daily_emp_works,date,NULL,NULL,intermediate_product_id,' . $request['intermediate_product_id'],

        ],[
            'intermediate_product_id.unique' => 'You already inserted this intermediate product today. Please delete in the table below first.',
            'intermediate_product_id.required' => 'The intermediate product is required',
        ]
        );

        $newD =  $this->builder->create($request->only($this->getCreatedColumns()));
        // dd($newD->intermediate_product_id);
        
        $theRelatedIP =  $newD->intermediate_product;
        // dd($theRelatedIP);
        // dd($newD->Status);
        if($newD->Status == 'OnGoing'){
            $theRelatedIP->Preparation = 'OnGoing';
            $theRelatedIP->save();
        }
        broadcast(new DailyEmpWorkEvent())->toOthers();

        return $newD;
        // if($request->Status == 'Completed'){
        //     $newD =  $this->builder->create($request->only($this->getCreatedColumns()));
        //     // update current Qty of the intermediate product if emp complete preparation
        //     if ($newD){
        //         $inter_p = Intermediate_product::find($request->intermediate_product_id);
        //         $inter_p->current_qty = $inter_p->current_qty + $newD->current_prepared_qty;
        //         //update preparation status of Inter_D
        //         if($inter_p->current_qty <= $inter_p->prepared_point){
        //             $inter_p->preparation = 'Yes';
        //         } else $inter_p->preparation = 'No';
        //         $inter_p->save();
        //     }
        //     return $newD;
        // } 
        // elseif ($request->Status == 'OnGoing')
        // {
        //     $newD =  $this->builder->create($request->only($this->getCreatedColumns()));
        //      // update current Qty of the intermediate product if emp complete preparation
            
        //         $inter_p = Intermediate_product::find($request->intermediate_product_id);
                
        //         $inter_p->preparation = 'OnGoing';
        //         $inter_p->save();
            
        //     return $newD;
        // } else {
        //     return ('no action taken');
        // }

      
      
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'user_id' => 'required',
            'Status' => 'required',
            'done_qty' => 'required|numeric',
            'current_prepared_qty' => 'required|numeric',
            'intermediate_product_id' => 'required'
        ]);
        $dateEmpProduct = explode(' ',$id);
        $theDate = $dateEmpProduct[0];
        $theEmpId = $dateEmpProduct[1];
        $thePreProductId = $dateEmpProduct[2];

        // dd($request->current_qty);
        // $updated_intermediate = $this->builder->find($id);

        // dd($updated_intermediate);
        $the_daily_emp_work_builder = Daily_Emp_Work::where('date',$theDate)
                                -> where('user_id',$theEmpId)
                                -> where('intermediate_product_id',$thePreProductId);
        $updatedSuccess =  $the_daily_emp_work_builder ->update(
            $request->only($this->getUpdatableColumns())
        );

        $the_daily_emp_work = $the_daily_emp_work_builder->first();

        // update status of table Intermediate Product according to status of daily_emp_work
        $theIP =  $the_daily_emp_work->intermediate_product; 
        if ($updatedSuccess == 1) {
            if($the_daily_emp_work->Status == "OnGoing") {
                $theIP->Preparation = "OnGoing";
                $theIP->save();
            }
            else {
                if($theIP->current_qty <= $theIP->prepared_point){
                    $theIP->Preparation = 'Yes';
                    $theIP->required_qty = $theIP->coverage -  $theIP->current_qty;  
                    $theIP->save();
                } elseif ($theIP->current_qty > $theIP->prepared_point){
                    $theIP->Preparation = 'No';
                    $theIP->required_qty = 0;
                    $theIP->save();
                } 
                elseif ($theIP->current_qty > $theIP->coverage){
                    $theIP->Preparation = 'No';
                    $theIP->required_qty = 0;
                    $theIP->save();
                } 
            } 
        }
        

        // if ($updatedSuccess == 1 & $intermediate->current_qty <= $intermediate->prepared_point){
        //     $intermediate->Status = 'Prepare';
        //     $intermediate->required_qty = $intermediate->coverage -   $intermediate->current_qty;  
        //     $intermediate->save();
        // } elseif ($updatedSuccess == 1 & $intermediate->current_qty > $intermediate->prepared_point){
        //     $intermediate->Status = '';
        //     $intermediate->required_qty = 0;
        //     $intermediate->save();
        // }
        broadcast(new DailyEmpWorkEvent())->toOthers();

        return $updatedSuccess;
    }

    public function updateNote($id, Request $request){

       
        $dateEmpProduct = explode(' ',$id);
        $theDate = $dateEmpProduct[0];
        $theEmpId = $dateEmpProduct[1];
        $thePreProductId = $dateEmpProduct[2];

        // dd($theDate.$theEmpId.$thePreProductId);

        // dd($request->current_qty);
        // $updated_intermediate = $this->builder->find($id);

        // dd($updated_intermediate);
        $the_daily_emp_work = Daily_Emp_Work::where('date',$theDate)
                                -> where('user_id',$theEmpId)
                                -> where('intermediate_product_id',$thePreProductId)
                                ->update(['Note'=> $request->note]);
       
        // // dd($the_daily_emp_work);
        // $the_daily_emp_work->Note = 'wow -w work';

        // $the_daily_emp_work->save();
        broadcast(new DailyEmpWorkEvent())->toOthers();
        return   $the_daily_emp_work;   

    }

    
    public function destroy($ids, Request $request)
    {
        if (!$this->allowDeletion) {
            return;
        }


        $arrayIds = explode(',',$ids);

        foreach ($arrayIds as $key => $value) {
            $dateEmpProduct = explode(' ',$value);
            $theDate = $dateEmpProduct[0];
            $theEmpId = $dateEmpProduct[1];
            $thePreProductId = $dateEmpProduct[2];
            $inter_p = Daily_Emp_Work::where('date',$theDate)
                -> where('user_id',$theEmpId)
                -> where('intermediate_product_id',$thePreProductId);
            if($inter_p) {
                $inter_p->delete();
            } else return 'record is undefined';
        }
        broadcast(new DailyEmpWorkEvent())->toOthers();



        // intend to implement but finally did not do it
        // if (count($arrayIds) == 1){

        //     $dateEmpProduct = explode(' ',$arrayIds[0]);
        //     $theDate = $dateEmpProduct[0];
        //     $theEmpId = $dateEmpProduct[1];
        //     $thePreProductId = $dateEmpProduct[2];

        //     // only allow to delete record having the date equal today and reverse back the quantiy of inter product
        //     $today = Carbon::now()->isoFormat('YYYY-MM-DD');
        //     // $sevenDaysAgo = Carbon::now()->subDays(7)->isoFormat('YYYY-MM-DD');
        //     // dd($sevenDaysAgo);
        //     if($today == $theDate) {
        //         $the_d_emp_w = Daily_Emp_Work::where('date',$theDate)
        //         -> where('user_id',$theEmpId)
        //         -> where('intermediate_product_id',$thePreProductId)->first();
        //         if($the_d_emp_w) {
        //             $the_d_emp_w->delete();
        //             // // before delete check status of the inter product
        //             // // if the deleted product has status of completed 
        //             // //--> reverse back previous quantity of intermediate product in table Intermediate_Product
        //             // if ($the_d_emp_w->Status == 'Completed'){                 
        //             //     $inter_p = Intermediate_product::find($thePreProductId);
        //             //     //reverse back the previous quantity
        //             //     $inter_p->current_qty = $inter_p->current_qty - $the_d_emp_w->current_prepared_qty;
        //             //     //reverse back preparation status of Inter_D
        //             //     // $inter_ps = Intermediate_product::latest();
        //             //     // dd($inter_ps);
    
    
        //             //     $inter_p->save();
        //             //     $the_d_emp_w->delete();
        //             // }
        //             // elseif ($the_d_emp_w->Status == 'OnGoing'){
        //             //     $the_second_d_em_w = Daily_Emp_Work::latest()
        //             //     ->where('intermediate_product_id',$thePreProductId)->get();
        //             //     // ->skip(2)->first();//Second row;
        //             //     // $inter_ps = Intermediate_product::latest()->skip(1)->first();//Second row;
        //             //     dd($the_second_d_em_w);
        //             // }

                    
        //         } else return 'record is undefined';

        //     }            
        //     else return('cannot delete the record that is not today');      
        // }
        // elseif (count($arrayIds) > 1)
        // {
        //     foreach ($arrayIds as $key => $value) {
        //         $dateEmpProduct = explode(' ',$value);
        //         // dd($dateEmpProduct);
        //         $theDate = $dateEmpProduct[0];
        //         $theEmpId = $dateEmpProduct[1];
        //         $thePreProductId = $dateEmpProduct[2];
        //         $the_d_emp_w = Daily_Emp_Work::where('date',$theDate)
        //             -> where('user_id',$theEmpId)
        //             -> where('intermediate_product_id',$thePreProductId)->first();
        //             // dd($the_d_emp_w->Status);
        //         if($the_d_emp_w) {
        //             $the_d_emp_w->delete();
        //             // before delete check status of the inter product
        //             // if the deleted product has status of completed 
        //             //--> reverse back previous quantity of intermediate product in table Intermediate_Product
        //             // if ($the_d_emp_w->Status == 'Completed'){                 
        //             //     $inter_p = Intermediate_product::find($thePreProductId);
        //             //     //reverse back the previous quantity
        //             //     $inter_p->current_qty = $inter_p->current_qty - $the_d_emp_w->current_prepared_qty;
        //             //     //reverse back preparation status of Inter_D
        //             //     $inter_ps = Intermediate_product::latest();
        //             //     // dd($inter_ps);
    
    
        //             //     $inter_p->save();
        //             //     $the_d_emp_w->delete();
        //             // }
                    
        //         } else return 'record is undefined';
        //     }
        // }
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
        Storage::put('public/daily_emp_work_images/'. $imageName, $imageNameResize->__toString());
        Storage::put('public/daily_emp_work_images/'. $thumbnailName, $thumbnailNameResize->__toString());

        $the_daily_emp_work = $this->builder->find($id);

        if ($the_daily_emp_work->thumbnail){
            $result_thumbnail_array = explode('/',$the_daily_emp_work->thumbnail);
            $old_thumbnail_name = $result_thumbnail_array[count($result_thumbnail_array)-1];
            Storage::delete([
            'public/daily_emp_work_images/'. $old_thumbnail_name,
            ]);
        }
        if ($the_daily_emp_work->image){
            $result_image_array = explode('/',$the_daily_emp_work->image);
            $old_image_name = $result_image_array[count($result_image_array)-1];
            Storage::delete([
            'public/daily_emp_work_images/'. $old_image_name,
            ]);
        }
        // save new image
        $the_daily_emp_work -> thumbnail = "/storage/daily_emp_work_images/".$thumbnailName;
        $the_daily_emp_work -> image = "/storage/daily_emp_work_images/".$imageName;
        $the_daily_emp_work -> save();

        return $the_daily_emp_work;
        // return "successfully saved";
    }

    

    protected function getRecords(Request $request)
    {
        $builder = $this->builder;
        // $builder->truncate();

        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }

        if (isset($request->user_id)) {
            $builder =   $builder->where('user_id','=',$request->user_id);
        }
        if (isset($request->intermediate_product_id)) {
            $builder =   $builder->where('intermediate_product_id','=',$request->intermediate_product_id);
        }
        if (isset($request->Status)) {
            $builder =   $builder->where('Status','=',$request->Status);
        }

      

        try {

            $dews = $builder->limit($request->limit)
                ->orderBy('created_at', 'desc')
                ->get($this->getRetrievedColumns())
                ->load(['intermediate_product','intermediate_product.permissions']);
          

            $dews_filtered_permission_array = [];
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
                    // dd($userPermissionIds);
                    // lopp to all daily employee works
                    foreach ($dews as $dew){
                        $theDew_Permissions = $dew->intermediate_product->permissions;
                        
                        //loop through all the assigened permission of the dew
                        foreach($theDew_Permissions as $theDew_Permission){
                            // if the permission of dew is one in the user permissions
                            if(in_array($theDew_Permission->id,$userPermissionIds)) {
                                array_push($dews_filtered_permission_array,$dew);
                                break;
                            }
                        }
                    }
                    // dd($dews_filtered_permission_array);
                } else {
                    // lopp to all goods and materials
                    foreach ($dews as $dew){
                        $theDew_Permissions = $dew->permissions;
                        //loop through all the assigened permission of the gm
                        foreach($theDew_Permissions as $theDew_Permission){
                            // if the permission of gm is the user permissions
                            if($theDew_Permission->id == $request->permission_id) {
                                array_push($dews_filtered_permission_array,$dew);
                                break;
                            }
                        }
                    }
                }
            }
            // refresh gms with filtered permissions
            if(!empty($dews_filtered_permission_array)){
                $dews = collect($dews_filtered_permission_array);
            }
            
            
            return Daily_Emp_WorkResourceDB::collection($dews);
            
          
        } catch (QueryException $e) {
            return [];
        }    
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

    public function getUserRoleOptions()
    {
        $user = auth()->user();       
        return $user->roles->map->only(['id', 'name']);
    }

    public function getIntermediate_ProductOptions()
    {
        $user = auth()->user();
        // get all roles of the user from pivot table user_role
        $user_roles = $user->roles;
        // get all permissions of the user from pivot table user_permission
        $user_permissions = $user->permissions;

        $userPermissionId =[];
        
        $returnArr = [];
        
        // get all permissions from the role of user
        foreach($user_roles as $role){
            foreach($role->permissions as $permission){
                // $userPermission[$permission->id] = $permission->name;
                array_push($userPermissionId,$permission->id);
            }
        }

        // get all permissions from the the pivot table of user_permission
        foreach($user_permissions as $permission)
        {
            if(!in_array($permission->id,$userPermissionId))    
            array_push($userPermissionId,$permission->id);
        }
       
        // foreach($user_roles as $role){
            $r = Intermediate_Product::select('id','name','required_qty')
            // ->where('status','Prepare')
            // ->where('active',1)
            // ->whereIn('permission_id',$userPermissionId)
            ->get();           
            

         
            foreach ($r as  $sr) {
                $returnArr[$sr['id']] = $sr;
            }
      
        // }
        // $r_array = json_decode(json_encode($r), true);

        // return $r;
        // return $r_array;
        return $returnArr;
        // return $user_roles;
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

    public function getAuthenticateduser()
    {
        $user = auth()->user();
        
        $authenticatedUser = [];

        $authenticatedUser['roles'] = $user->roles;
        $authenticatedUser['name'] = $user->name;
        $authenticatedUser['username'] = $user->username;
        $authenticatedUser['email'] = $user->email;
        $authenticatedUser['permissions'] = $user->getPermissions();
        
        return $authenticatedUser;
    }
    
    public function getStatusOptions()
    {
        $returnArr = ['OnGoing','Completed'];
        return $returnArr;
    }

    public function getNeedPrepareIntermediate_ProductOptions()
    {
        $user = auth()->user();  
        // dd($user->getIntermediateProducts());      
        return $user->getIntermediateProductOptions();
    }
    public function getNeedPrepareIntermediate_Products()
    {
        $user = auth()->user();  
        // dd($user->getIntermediateProducts());      
        return $user->getIntermediateProducts();
    }
   

}
