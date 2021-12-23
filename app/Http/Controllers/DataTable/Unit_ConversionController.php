<?php

namespace App\Http\Controllers\DataTable;


use PDF;
use App\Models\Stock\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Stock\Goods_material;
use App\Models\Stock\Unit_Conversion;
use Illuminate\Support\Facades\Storage;
use App\Models\Stock\Intermediate_product;
use App\Http\Resources\Stock\Unit_ConversionResourceDB;

class Unit_ConversionController extends DataTableController
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
          'unitOptions'=> $this->getUnitOptions(),
          'GMOptions'=> $this->getGMOptions(),
          'gmProducts'=> $this->getGMs(),
          'IPOptions'=> $this->getIPOptions(),
          'intermediateProducts'=> $this->getIPs(),
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
        return Unit_Conversion::query();
    }

    public function getCustomColumnsNames()
    {
        return [
            'goods_material_id' => 'Good Material',
            'intermediate_product_id' => 'Inter Product',        
            'rate' => 'Rate(IP/GM)',        
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id',
            'intermediate_product_id',
            'ip_unit',
            'goods_material_id',
            'gm_unit',        
            'rate'
        ];
    }
    public function getRetrievedColumns()
    {
        return [
            'id',
            'intermediate_product_id',
            'goods_material_id' ,
            'rate'
        ];
    }

    public function getUpdatableColumns()
    {
        return [
            'intermediate_product_id','goods_material_id' ,'rate'
        ];
    }
    

    protected function getRecords(Request $request)
    {
        $builder = $this->builder;

        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }

        // if (isset($request->locationId)) {
        //     $builder =   $builder->where('id','=',$request->locationId);
        // }
  
        try {
            $unit_conversion = $builder
                ->limit($request->limit)
                ->orderBy('id', 'asc')
                ->get($this->getRetrievedColumns());
                // ->paginate(2);
           
            // $gmPermissions = $builder->permissions->map->only(['id', 'name']);
            // dd($gmPermissions);
    

            $pr = Unit_ConversionResourceDB::collection(
                $unit_conversion
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
            'goods_material_id' => 'required',
            'goods_material_id' => 'required',
        ]);

        return $this->builder->create($request->only($this->getUpdatableColumns()));
        
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'goods_material_id' => 'required',
            'goods_material_id' => 'required',
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

    
    // Generate PDF
    public function createPDF() {
        // retreive all records from db
        $unit_conversion = Location::all();
        // dd($data);
  
        // share data to view
        // view()->share('employee',$data);
        $pdf = PDF::loadView('employee', compact('unit_conversion'));
  
        // download PDF file with download method
        return $pdf->download('pdf_file.pdf');
      }
      
    public function getGMOptions()
    {
        // $c = Goods_material::all()->sortBy('name');
        // dd($c);
        $c = Goods_material::orderBy('name')->get();
       
        $returnArr = [];
        foreach ($c as  $sc) {
            $returnArr[$sc['id']] = [$sc['name'], $sc['unit_id']];
        }
        // dd( $returnArr);
        return $returnArr;
    }
    public function getGMs()
    {
        $c = Goods_material::all('id','name','unit_id')->sortBy('name');

        $returnArr = [];
        foreach ($c as  $sc) {
            // $returnArr[$sc['id']] = [$sc['name'], $sc['unit_id']];
            $newArray=[];
            $newArray['id'] = $sc['id'];
            $newArray['name'] = $sc['name'];
            $newArray['unit_id'] = $sc['unit_id'];
            array_push($returnArr, $newArray);
             
        }
        return $returnArr;
    }
    public function getIPOptions()
    {
        $c = Intermediate_product::all('id','name','unit_id')->sortBy('name');

        $returnArr = [];
        foreach ($c as  $sc) {
            $returnArr[$sc['id']] = [$sc['name'], $sc['unit_id']];
        }
        return $returnArr;
    }

    public function getIPs()
    {
        $c = Intermediate_product::all('id','name','unit_id')->sortBy('name');

        $returnArr = [];
        foreach ($c as  $sc) {
            // $returnArr[$sc['id']] = [$sc['name'], $sc['unit_id']];
            $newArray=[];
            $newArray['id'] = $sc['id'];
            $newArray['name'] = $sc['name'];
            $newArray['unit_id'] = $sc['unit_id'];
            array_push($returnArr, $newArray);
             
        }
        return $returnArr;
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
}
