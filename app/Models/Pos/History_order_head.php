<?php

namespace App\Models\Pos;

use Carbon\Carbon;
use App\Models\Pos\History_Order_Detail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class History_Order_Head extends Model
{
    protected $table = 'history_order_head';
    public $timestamps = false;
    protected $connection ='mysql2';
    protected $primaryKey = "order_head_id";
    // public $orderInfoIds = [
    //     1239,1240,1241,1242,1243,1244,1245,1248,
    //     1272,1273,1274,1275,1276,1277,1278,1279,1280,
    //     6004,6111,6210,6211,6212,

    // ];
    protected $waitingTimeIds = [
        1272,1273,1274,1275,1276,1277,1278,1279,1280
    ];
    public $waitingTime = "Not Set";
    public $orderInfo = null;
    public $orderDetails = null;

    use HasFactory;
    protected $fillable = [
        'order_head_id',
        'check_number',
        // 'rvc_center_id',
        // 'rvc_center_name',
        // 'table_id',
        // 'table_name',
        'check_id',
        'open_employee_id',
        'open_employee_name',
        'customer_num',
        'customer_id',
        'customer_name',
        'pos_device_id',
        'pos_name',
        'order_start_time',
        'order_end_time',
        // 'should_amount',
        // 'return_amount',
        // 'discount_amount',
        // 'actual_amount',
        // 'print_count',
        'status',
        'eat_type',
        'check_name',
        // 'original_amount',
        // 'service_amount',
        // 'edit_time',
        // 'party_id',
        'edit_employee_name',
        'remark',
        'is_make',
        'delivery_info',
        // 'kds_show',
        // 'kds_time',
        // 'tax_amount',
        // 'raw_talbe',
    ];

    public function history_order_details()
    {
        return $this->hasMany(History_Order_Detail::class,'order_head_id','order_head_id');
    }

    public function getOrderDetails(){

        // $order = Order_Head::where('order_head_id',$this->order_head_id)->first();
        // $orderDetailsInTheOrder = History_Order_Detail::where('order_head_id',$this->order_head_id)
        $orderDetailsInTheOrder = $this->history_order_details
            ->where('condiment_belong_item',0)
            ->where('discount_id',9);
        // ->whereIn('order_head_id', $this->orderInfoIds)
        // ->where('return_time',null)
            // ->get();

       
        $order_Info = [];
        $deepFried = [];
        $rice = [];
        $soup = [];
        $poutry = [];
        $meat = [];
        $seaFood = [];
        $omelette = [];
        $vegetarian = [];
        $creator = [];
        $noodle = [];
        $familyPack = [];
        $lunchSpecial = [];
        $miscellaneous = [];
        $drinks = [];
        $gift = [];

        foreach ($orderDetailsInTheOrder as $orderDetail) {
            // var_dump($orderDetail->menu_item_id);
            if($orderDetail->menu_item_id > 0){
                // $theMenuItem = Menu_item::where('item_id',$orderDetail->menu_item_id)->first();        
                $theMenuItem = $orderDetail->menu_item;   
                // $theFamilyGroupOftheItem = Family_Group::where('family_group_id',$theMenuItem->family_group)->first();
                $theFamilyGroupOftheItem = $theMenuItem->fam_group;

            // add condiments, descriptions to the item
            $theItemId = $orderDetail->order_detail_id;
            // $condimentsOfTheItem = History_Order_Detail::where('condiment_belong_item',$theItemId)->get();
            $condimentsOfTheItem = $orderDetail->condiments;
            $orderDetail->condiments =  $condimentsOfTheItem;
            // array_push($extraInfo,$orderDetail);
            foreach ($condimentsOfTheItem as $condiment) {
                // var_dump($condiment->menu_item_name);
                if(in_array($condiment->menu_item_id, $this->waitingTimeIds)){
                    $this->waitingTime = $condiment->menu_item_name;
                }
            }
            switch ($theFamilyGroupOftheItem->family_group_name) {
                case 'Order Info':
                    array_push($order_Info,$orderDetail);
                    break;
                case 'Deep Fried':
                    $deepFried = $this->addOrderDetailToArrayGroup($deepFried,$orderDetail);
                    break;
                case 'Rice':
                    $rice = $this->addOrderDetailToArrayGroup($rice,$orderDetail);
                    break;
                case 'Soup':
                    $soup = $this->addOrderDetailToArrayGroup($soup,$orderDetail);
                    break;
                case 'Poutry':
                    $poutry = $this->addOrderDetailToArrayGroup($poutry,$orderDetail);
                    break;
                case 'Meat':
                    $meat = $this->addOrderDetailToArrayGroup($meat,$orderDetail);
                    break;
                case 'Seafood':
                    $seaFood = $this->addOrderDetailToArrayGroup($deepFried,$orderDetail);
                    break;
                case 'Omelette':
                    $omelette = $this->addOrderDetailToArrayGroup($omelette,$orderDetail);
                    break;
                case 'Vegetarian':
                    $vegetarian = $this->addOrderDetailToArrayGroup($vegetarian,$orderDetail);
                    break;
                case 'Creator':
                    $creator = $this->addOrderDetailToArrayGroup($creator,$orderDetail);
                    break;
                case 'Noodle':
                    $noodle = $this->addOrderDetailToArrayGroup($noodle,$orderDetail);
                    break;
                case 'Family Pack':
                    $familyPack = $this->addOrderDetailToArrayGroup($familyPack,$orderDetail);
                    break;
                case 'Lunch Special':
                    $lunchSpecial = $this->addOrderDetailToArrayGroup($lunchSpecial,$orderDetail);
                    break;
                case 'Miscellaneous':
                    $miscellaneous = $this->addOrderDetailToArrayGroup($miscellaneous,$orderDetail);
                    break;
                case 'Drinks':
                    // $drinks = $this->addOrderDetailToArrayGroup($drinks,$orderDetail);
                    array_push($drinks,$orderDetail);
                    break;
                case 'Gift':
                    // $gift = $this->addOrderDetailToArrayGroup($gift,$orderDetail);
                    array_push($gift,$orderDetail);
                    break;
                
                default:
                    array_push($order_Info,$orderDetail);
                    break;
            }
        }
    }
        $History_order_details=[$order_Info,$deepFried,$rice,$soup,$poutry,$meat,$seaFood,$omelette,$vegetarian,$creator,$noodle,$familyPack,$lunchSpecial,$miscellaneous,$drinks,$gift];
        
        return $History_order_details;

        // return   $duration ;
    }

    public function addOrderDetailToArrayGroup($theFamilyGroupArray,$theOrderDetail) {
        $itemAlreadyExit = 0;
        //Loop through the array to see if there is any item having the same name
        for ($i=0; $i < count($theFamilyGroupArray); $i++) { 
            # code...
            $theCurrentItemInArray = $theFamilyGroupArray[$i];
            //check if they have the same name, unit and description
            if($theCurrentItemInArray->menu_item_name == $theOrderDetail->menu_item_name && $theCurrentItemInArray->unit ==  $theOrderDetail->unit && $theCurrentItemInArray->description == $theOrderDetail->description){
                // check the same condiment ?
                if(count($theOrderDetail->condiments) == 0 && count($theCurrentItemInArray->condiments) == 0){
                    $theCurrentItemInArray->quantity = $theCurrentItemInArray->quantity + $theOrderDetail->quantity;
                    $itemAlreadyExit =1;
                    break;
                } elseif(count($theOrderDetail->condiments) > 0 && count($theOrderDetail->condiments) == count($theCurrentItemInArray->condiments)){
                    $matchAll = 1;
                    for ($k=0; $k < count($theOrderDetail->condiments) ; $k++) { 
                        $condimentOftheOrderDetail_k = $theOrderDetail->condiments[$k];
                        $condimentOfCurrentItemInArray_k = $theCurrentItemInArray->condiments[$k];

                        if($condimentOftheOrderDetail_k->menu_item_name != $condimentOfCurrentItemInArray_k->menu_item_name){
                            $matchAll = 0;
                            break;
                        }
                    }
                    //if the condiments is also match, then 
                    if ( $matchAll == 1){
                        $theCurrentItemInArray->quantity = $theCurrentItemInArray->quantity + $theOrderDetail->quantity;
                        $itemAlreadyExit =1;
                        break;
                    }
                }                     
                    
            } 
        }
        if($itemAlreadyExit == 0){
            array_push($theFamilyGroupArray,$theOrderDetail);
        }
        return $theFamilyGroupArray;
       
    }
  
    public function getWaitingTime(){

            // $orderDetailsInTheOrder = History_Order_Detail::where('order_head_id',$this->order_head_id)
            $orderDetailsInTheOrder = $this->history_order_details;

        // $orderDetailsInTheOrder = History_Order_Detail::where('order_head_id',$this->order_head_id)->get();
        
        // $orderDetails = [];

        foreach ($orderDetailsInTheOrder as $orderDetail) {
            if (in_array($orderDetail->menu_item_id, $this->waitingTimeIds) ){
                return $orderDetail->menu_item_name;  
                // array_push($orderDetails,$orderDetail);            
            }           
        }
       
        // return $orderDetails;
    }
    public function getHandleTime(){

        if($this->getWaitingTime()){
        $waitingTimeArray =explode("-",$this->getWaitingTime());
        $waitingTime = $waitingTimeArray[0];
        
        // return$waitingTime;
        $orderTimeInCarbon = Carbon::parse($this->order_start_time, 'UTC');

        $handleTime = $orderTimeInCarbon->addMinutes($waitingTime)->format('Y-m-d H:i:s');
        return $handleTime;
        } else return null;
    }
    public function getRemainingTime(){

        // $currentTime = Carbon::now()
        if($this->getHandleTime()){
            $pickupTime = Carbon::parse($this->getHandleTime());
            $currentTime= Carbon::now('Australia/Brisbane');

            // var_dump('Pick up time is: ' . $pickupTime);
            // var_dump('Pick up time is: ' .$currentTime);
            $remainingTime = "";

            if ($pickupTime > $currentTime) {         
                $remainingTime = (string) $pickupTime->diffInMinutes($currentTime);
            } else { 
                $remainingTime = $pickupTime->diffInMinutes($currentTime);
                return 'Late ' . $remainingTime;
            }

            // $receivedDate=$dateInBrisbane->isoFormat('MMMDoYY');
            return $remainingTime;
                // return $diff;
        } else return 'Null';
    }
}
