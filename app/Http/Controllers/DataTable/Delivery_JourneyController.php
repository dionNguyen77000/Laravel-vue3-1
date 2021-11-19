<?php

namespace App\Http\Controllers\DataTable;


use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Delivery\Delivery_Journey;
use App\Models\Delivery\Delivery_Setting;
use App\Http\Resources\Delivery\Delivery_JourneyResourceDB;
use App\Models\Delivery\Delivery_Detail;
use App\Models\Delivery\Delivery_Travel_Time;

class Delivery_JourneyController extends DataTableController
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
          'created' => array_values($this->getCreatedColumns()),
          'records' => $this->getRecords($request),
          'onGoingJourneys' => $this->getonGoingJourneys($request),
          'custom_columns' => $this->getCustomColumnsNames(),
          'userPermissionOptions'=> $this->getUserPermissionOptions(),
          'userOptions'=> $this->getUserOptions(),
          'userRoleOptions'=> $this->getUserRoleOptions(),
          'zoneOptions'=> $this->getZoneOptions(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]
      ]);
    }

    public function builder()
    {
        return Delivery_Journey::query();
    }

    public function getCustomColumnsNames()
    {
        return [
            'fuel_payment'=>'fuel',
            'actual_return'=>'return',
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id',
            'date', 
            'driver',
            'route',
            'departure',
            'duration',
            'est_return',
            'actual_return',
            'fuel_payment',
            
            'approve'

        
        ];
    }
    public function getRetrievedColumns()
    {
        return [
            'id',
            'date', 
            'driver',
            // 'route',
            // 'departure',
            // 'estimated_duration',
            // 'estimated_return',
            'actual_return',
            
            // 'fuel_payment',
            'approve'
        ];
    }

    
    public function getCreatedColumns()
    {
        return [
            'date', 
            'driver',
            // 'journey',
            // 'departure',
            // 'estimated_duration',
            // 'estimated_return',
            'actual_return',
            
            // 'fuel_payment',
            'approve'
        ];
    }

    public function getUpdatableColumns()
    {
        return [
            'date', 
            'driver',
            // 'journey',
            // 'departure',
            // 'estimated_duration',
            // 'estimated_return',
            'actual_return',
            
            // 'fuel_payment',
            'approve'
        ];
    }
    

    protected function getRecords(Request $request)
    {
        $builder = $this->builder;

        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }

        if ($this->hasSearchQuery_1($request)) {
            $builder = $this->buildSearch_1($builder, $request);
        }

        if (isset($request->driver) && $request->driver != 'All')  {
            $builder =   $builder->where('driver','=',$request->driver);
        }
        if (isset($request->approve) && $request->approve != 'All')  {
            $builder =   $builder->where('approve','=',$request->approve);
        }
  
        try {
            $locations = $builder
                            ->limit($request->limit)
                            ->orderBy('id', 'asc')
                            ->get($this->getRetrievedColumns())
                            ->load(['delivery_details']);
                            // ->paginate(2);
           
            // $gmPermissions = $builder->permissions->map->only(['id', 'name']);
            // dd($gmPermissions);
    

            $pr = Delivery_JourneyResourceDB::collection(
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
    protected function getonGoingJourneys()
    {
        $delivery_journeys = Delivery_Journey::where('actual_return',null)
        ->with('delivery_details')
        ->orderBy('id', 'desc')->get();

        
        // $returnDeliveryJourneyArrays = [];
        // foreach ($delivery_journeys as $key => $delivery_journey) {
        //     $deliveryDetails = $delivery_journey->delivery_details->map->only(['zone', 'departure','estimated_duration_arrival','estimated_arrival','actual_arrival','estimated_duration_return','estimated_return']);   

        //     array_push(  $returnDeliveryJourneyArrays,$deliveryDetails);
        // }
        // dd()
        // $deliveryDetails = Delivery_Detail::where('actual_return',null)
        //                                     ->get()->pluck('zone','estimated_arrival');

        // return $returnDeliveryJourneyArrays;
        return $delivery_journeys;
    }
    public function store(Request $request)
    {
        // dd(now());
        // dd($request->delivery_details);
        $user = auth()->user();       
        $new_delivery_journey = new Delivery_Journey();
        $new_delivery_journey->date=$request->date;;
        $new_delivery_journey->driver= $user->name;
        $new_delivery_journey->mobile= $user->mobile;
        $new_delivery_journey->status= 'OnGoing';
        $new_delivery_journey->approve =  'No';
        $new_delivery_journey->save();
        if(count($request->delivery_details)>0){
            // loop through 
            
            // $current_time_destination_track = Carbon::now();
            $current_time_destination_track = new Carbon($request->departure);
            // dd($current_time_destination_track);
            // $current_time_destination_track = new Carbon($request->departure->format('Y-m-d H:i:s.u'),  $request->departure->getTimezone());

            $previous_zone_id = 1;
            $previous_zone_name = 'YAR';
            foreach ($request->delivery_details as $delivery_detail) {
                // global $current_time_destination_track;

                $new_delivery_detail = new Delivery_Detail();
                $the_delivery_setting = Delivery_Setting::where('zone',$delivery_detail['zone'])->first();
                $new_delivery_detail->delivery_journey_id = $new_delivery_journey->id;
                $new_delivery_detail->order_number =  $delivery_detail['order_number'];
                $new_delivery_detail->suburb =  $the_delivery_setting->suburb;
                $new_delivery_detail->journey =  $previous_zone_name .'->' .$the_delivery_setting->zone;

               
                $new_delivery_detail->zone =  $delivery_detail['zone'];
                $new_delivery_detail->cash_received =  $delivery_detail['cash_received'];
                $new_delivery_detail->change =  $delivery_detail['change'];
                $new_delivery_detail->fuel_cost =  $the_delivery_setting['fuel_cost'];
                $new_delivery_detail->approve =  'No';
                
                
            
                    $new_delivery_detail->departure =   $current_time_destination_track;

                    $current_time_destination_track = new Carbon($new_delivery_detail->departure->format('Y-m-d H:i:s.u'),  $new_delivery_detail->departure->getTimezone());
                   
                    $the_delivery_setting = Delivery_Setting::where('zone',$delivery_detail['zone'])->first();

                    //find the travel delivery betwen the suburb and previous suburb                   
                    $the_delivery_travel = Delivery_Travel_Time::where('destination_one_id',$previous_zone_id)
                                                            ->where('destination_two_id',$the_delivery_setting->id)
                                                            ->first();
                    $new_delivery_detail->estimated_duration_arrival =  $the_delivery_travel->estimated_duration;
                    $new_delivery_detail->estimated_arrival =  $current_time_destination_track->addMinutes($new_delivery_detail->estimated_duration_arrival);
                    $current_time_destination_track = new Carbon($new_delivery_detail->estimated_arrival->format('Y-m-d H:i:s.u'),  $new_delivery_detail->estimated_arrival->getTimezone());
                      //reasign new previous suburb 
                    $previous_zone_id = $the_delivery_setting->id;
                    $previous_zone_name = $the_delivery_setting->zone;
                  
                    // if it is the final destination - setup return esitmation 
                    if($delivery_detail['index'] == (count($request->delivery_details)-1)){
                        //find the travel delivery betwen the suburb and the restaurant
                        $the_delivery_travel = Delivery_Travel_Time::where('destination_one_id',$previous_zone_id)
                        ->where('destination_two_id',1)
                        ->first();
                        // $current_time_destination_track = new Carbon($new_delivery_detail->estimated_arrival->format('Y-m-d H:i:s.u'),  $new_delivery_detail->estimated_arrival->getTimezone());
                        $new_delivery_detail->estimated_duration_return =  $the_delivery_travel->estimated_duration;
                        $new_delivery_detail->estimated_return =  $current_time_destination_track->addMinutes($new_delivery_detail->estimated_duration_return);
                    }            
                $new_delivery_detail->save();

            }
        

        
        }
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'driver' => 'required',
        ]);

        $the_delivery_journey = Delivery_Journey::find($id);

        $the_delivery_journey -> date = $request->date;
        $the_delivery_journey -> driver = $request->driver;
        $the_delivery_journey -> actual_return = $request->actual_return;
        $the_delivery_journey -> approve = $request->approve;

        if($the_delivery_journey->save()){
            //update the return of delivery_detail
            if($the_delivery_journey -> actual_return) {
                $the_last_delivery_detail = $the_delivery_journey->delivery_details->last();
                $the_last_delivery_detail->actual_return = $the_delivery_journey -> actual_return;
                $the_last_delivery_detail->save();
            }
        }

        return $the_delivery_journey;
        // return $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
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

    
    public function getUserOptions()
    {
        $r = User::all('id','name');

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

   
    public function getZoneOptions()
    {
        $delivery_settings = Delivery_Setting::all();
        return $delivery_settings->map->only(['zone']);
    }

    
    // Generate PDF
    public function createPDF() {
        // retreive all records from db
        $locations = Delivery_Journey::all();
        // dd($data);
  
        // share data to view
        // view()->share('employee',$data);
        $pdf = PDF::loadView('employee', compact('locations'));
  
        // download PDF file with download method
        return $pdf->download('pdf_file.pdf');
      }
}
