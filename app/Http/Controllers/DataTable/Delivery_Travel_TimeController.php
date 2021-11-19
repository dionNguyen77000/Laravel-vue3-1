<?php

namespace App\Http\Controllers\DataTable;


use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Delivery\Delivery_Travel_Time;
use App\Http\Resources\Delivery\Delivery_JourneyResourceDB;
use App\Http\Resources\Delivery\Delivery_SettingResourceDB;
use App\Http\Resources\Delivery\Delivery_Travel_TimeResourceDB;
use App\Models\Delivery\Delivery_Setting;

class Delivery_Travel_TimeController extends DataTableController
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
          'custom_columns' => $this->getCustomColumnsNames(),
          'userPermissionOptions'=> $this->getUserPermissionOptions(),
          'userRoleOptions'=> $this->getUserRoleOptions(),
          'destinationOptions'=> $this->getDestinationOptions(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]
      ]);
    }

    public function builder()
    {
        return Delivery_Travel_Time::query();
    }

    public function getCustomColumnsNames()
    {
        return [
            'destination_one_id' => 'destination_one',
            'destination_two_id' => 'destination_two'
        ];
    }

    public function getDisplayableColumns()
    {
        return [
        'id',
        'destination_one_id', 
        'destination_two_id',
        'estimated_duration',
        ];
    }
    public function getRetrievedColumns()
    {
        return [
            'id',
            'destination_one_id', 
            'destination_two_id',
            'estimated_duration',
        ];
    }

    
    public function getCreatedColumns()
    {
        return [
    //    'id',
        'destination_one_id', 
        'destination_two_id',
        'estimated_duration',
        ];
    }

    public function getUpdatableColumns()
    {
        return [
    //    'id',
        'destination_one_id', 
        'destination_two_id',
        'estimated_duration',
        ];
    }
    

    protected function getRecords(Request $request)
    {
        $builder = $this->builder;

        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }

        if (isset($request->selectedDestinationOne) && $request->selectedDestinationOne != 'All') {
            $builder =   $builder->where('destination_one_id','=',$request->selectedDestinationOne);
        }
        if (isset($request->selectedDestinationTwo) && $request->selectedDestinationTwo != 'All') {
            $builder =   $builder->where('destination_two_id','=',$request->selectedDestinationTwo);
        }
  
        try {
            $locations = $builder
                            ->limit($request->limit)
                            ->orderBy('id', 'asc')
                            ->get($this->getRetrievedColumns());
                            // ->paginate(2);
           
            // $gmPermissions = $builder->permissions->map->only(['id', 'name']);
            // dd($gmPermissions);
    

            $pr = Delivery_Travel_TimeResourceDB::collection(
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
            'destination_one_id' => 'required',
            'destination_two_id' => 'required',
        ]);

        return $this->builder->create($request->only($this->getUpdatableColumns()));
        
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'destination_one_id' => 'required',
            'destination_two_id' => 'required',
        ]);

        return $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
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

    public function getDestinationOptions()
    {
        $c = Delivery_Setting::all('id','zone');

        $returnArr = [];
        foreach ($c as  $sc) {
            $returnArr[$sc['id']] = $sc['zone'];
        }
        return $returnArr;
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
