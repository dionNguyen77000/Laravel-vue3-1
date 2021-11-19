<?php

namespace App\Http\Controllers\DataTable;

use Image;
// use Barryvdh\DomPDF\PDF;
use PDF;
use Illuminate\Http\Request;
use App\Models\Stock\Location;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Stock\LocationResourceDB;


class LocationController extends DataTableController
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
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]
      ]);
    }

    public function builder()
    {
        return Location::query();
    }

    public function getCustomColumnsNames()
    {
        return [
            'img_thumbnail'=>'image',
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id','name' ,'img_thumbnail',
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
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            'name','img_thumbnail'
        ];
    }
    

    protected function getRecords(Request $request)
    {
        $builder = $this->builder;

        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }

        if (isset($request->locationId)) {
            $builder =   $builder->where('id','=',$request->locationId);
        }
  
        try {
            $locations = $builder
                ->limit($request->limit)
                ->orderBy('id', 'asc')
                ->get($this->getRetrievedColumns());
                // ->paginate(2);
           
            // $gmPermissions = $builder->permissions->map->only(['id', 'name']);
            // dd($gmPermissions);
    

            $pr = LocationResourceDB::collection(
                $locations
                // ->get($this->getDisplayableColumns())
            );
            // dd($pr);
            // return $ $goods_materials_builder;
            return $pr;
            
          
        } catch (QueryException $e) {
            return [];
        }    
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:units,name',
        ]);

        return $this->builder->create($request->only($this->getUpdatableColumns()));
        
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:units,name,' . $id,
        ]);

        return $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
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
        // Storage::put("public/location_images/". $imageName, $imageNameResize->__toString());
        // Storage::put("public/location_images/". $thumbnailName, $thumbnailNameResize->__toString());

        $dateInBrisbane= Carbon::now('Australia/Brisbane');
        $receivedDate=$dateInBrisbane->isoFormat('MMMDoYY'); 

        $theLocation= $this->builder->find($silderImageidArray[0]);

        // deal with second or third invoice image
        // rand(1,999) to create a random number between 1 and 999 so the name of image is different everytime generate
        // a new image --> this help to solve the proble of catching old image everytime update new image.
        if(count($silderImageidArray)>1){
            $img_number = $silderImageidArray[1];
            $imageName = $theLocation->name.rand(1,999).'_'.$img_number.'jpg';


            //delete old image
            if ($img_number == 2) {
                if ($theLocation->img_two){
                    $result_image_array = explode('/',$theLocation->img_two);
                    $old_image_name = $result_image_array[count($result_image_array)-1];
                    Storage::delete([
                    "public/location_images/". $old_image_name,
                    ]);
                }
                Storage::put("public/location_images/". $imageName, $imageNameResize->__toString());
                // save new image path to database
                $theLocation -> img_two = "/storage/location_images/".$imageName;
                $theLocation -> save();
            } else if ($img_number == 3) {
                if ($theLocation->img_three){
                    $result_image_array = explode('/',$theLocation->img_three);
                    $old_image_name = $result_image_array[count($result_image_array)-1];
                    Storage::delete([
                    "public/location_images/". $old_image_name,
                    ]);
                }
                Storage::put("public/location_images/". $imageName, $imageNameResize->__toString());
                $theLocation -> img_three = "/storage/location_images/".$imageName;
                $theLocation -> save();
            }
          
        }        
        // deal with first image 
        else {
           
            //delete old image in folder
            if ($theLocation->img_thumbnail){
                $result_thumbnail_array = explode('/',$theLocation->img_thumbnail);
                $old_thumbnail_name = $result_thumbnail_array[count($result_thumbnail_array)-1];
                Storage::delete([
                "public/location_images/". $old_thumbnail_name,
                ]);
            }
            if ($theLocation->img){
                $result_image_array = explode('/',$theLocation->img);
                $old_image_name = $result_image_array[count($result_image_array)-1];
                Storage::delete([
                "public/location_images/". $old_image_name,
                ]);
            }

            $randomNumber = rand(1,999);
            $imageName = $theLocation->name.$randomNumber.'.jpg';
            $thumbnailName = $theLocation->name.'_thumbnail_'.$randomNumber.'.jpg';

            Storage::put("public/location_images/". $imageName, $imageNameResize->__toString());
            Storage::put("public/location_images/". $thumbnailName, $thumbnailNameResize->__toString());

            // save new image
            $theLocation -> img_thumbnail = "/storage/location_images/".$thumbnailName;
            $theLocation -> img = "/storage/location_images/".$imageName;
            $theLocation -> save();
        }
        return $theLocation;
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

    
    // Generate PDF
    public function createPDF() {
        // retreive all records from db
        $locations = Location::all();
        // dd($data);
  
        // share data to view
        // view()->share('employee',$data);
        $pdf = PDF::loadView('employee', compact('locations'));
  
        // download PDF file with download method
        return $pdf->download('pdf_file.pdf');
      }
}
