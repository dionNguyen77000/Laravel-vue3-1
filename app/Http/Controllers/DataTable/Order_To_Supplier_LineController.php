<?php

namespace App\Http\Controllers\DataTable;

use Image;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Models\Stock\Unit;
use Illuminate\Http\Request;
use App\Models\Stock\Category;
use App\Models\Stock\Supplier;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

use App\Http\Controllers\Controller;
use App\Models\Stock\Goods_material;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Stock\Orders_To_Supplier;
use App\Models\Stock\Order_To_Supplier_Line;
use App\Http\Resources\Stock\Order_To_Supplier_LineResourceDB;

class Order_To_Supplier_LineController extends DataTableController
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
         'permissionOptions'=> $this->getPermissionOptions(),
          'roleOptions'=> $this->getRoleOptions(),
          'goods_materialOptions'=> $this->getGoods_MaterialOptions(),
          'supplierOptions'=> $this->getSupplierOptions(),
          'categoryOptions'=> $this->getCategoryOptions($request),
          'categoryOptions'=> $this->getCategoryOptions($request),
          'allCategoryOptions'=> $this->getAllCategoryOptions($request),
          'unitOptions'=> $this->getUnitOptions(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]

      ]);
    }

    public function builder()
    {
        return Order_To_Supplier_Line::query();
    }

    
    public function getCustomColumnsNames()
    {
        return [

            'orders_to_supplier_id' => 'Order',
            'o_unit_quantity' => 'quantity',
            'o_unit_price' => 'price/unit',
            'o_line_price' => 'subtotal',
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id',
            'orders_to_supplier_id',
            'goods_material',
            'unit',
            'o_unit_quantity',
            'o_unit_price',
            'o_line_price',
            'category',
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            'id',
            'orders_to_supplier_id',
            'goods_material',
            'unit',
            'o_unit_quantity',
            'o_unit_price',
            'o_line_price',
            'category',
        ];
    }

    public function getCreatedColumns()
    {
        return [
            // 'id',
            'orders_to_supplier_id',
            'goods_material',
            'unit',
            'o_unit_quantity',
            'o_unit_price',
            'o_line_price',
            'category',
        ];
    }
    
    public function store(Request $request)

    {
        $this->validate($request, [
            'orders_to_supplier_id' => 'required|numeric',
            // 'goods_material_id' => 'required|numeric'   
        ]);

        $newD =  $this->builder->create($request->only($this->getCreatedColumns()));

        return $newD;
  
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'orders_to_supplier_id' => 'required',
            'goods_material_id' => 'required',
        ]);
        $dateEmpProduct = explode(' ',$id);
        $theDate = $dateEmpProduct[0];
        $theEmpId = $dateEmpProduct[1];
        $thePreProductId = $dateEmpProduct[2];

        // dd($request->current_qty);
        // $updated_intermediate = $this->builder->find($id);

        // dd($updated_intermediate);
        $inter_p = Order_To_Supplier_Line::where('date',$theDate)
                                -> where('user_id',$theEmpId)
                                -> where('intermediate_product_id',$thePreProductId);
        // dd($inter_p);
        $updatedSuccess =  $inter_p ->update(
            $request->only($this->getUpdatableColumns())
        );
        return $updatedSuccess;
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
            $inter_p = Order_To_Supplier_Line::where('date',$theDate)
                -> where('user_id',$theEmpId)
                -> where('intermediate_product_id',$thePreProductId);
            if($inter_p) {
                $inter_p->delete();
            } else return 'record is undefined';
        }
    }
    


    protected function getRecords(Request $request)
    {
        $builder = $this->builder;


        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }

        if (isset($request->user_id)) {
            $builder =   $builder->where('user_id','=',$request->user_id);
        }

        if (isset($request->category)) {
            $builder =   $builder->where('category','=',$request->category);
        }
        
        // if (isset($request->order_to_supplierId)) {
        //     $builder =   $builder->where('orders_to_supplier_id','=',$request->order_to_supplierId);
        // }
        
        
    
        try {
            return Order_To_Supplier_LineResourceDB::collection(
                $builder->limit($request->limit)
                ->where('orders_to_supplier_id','=',$request->order_to_supplierId)
                ->orderBy('created_at', 'desc')
                ->get($this->getDisplayableColumns())
            );
            
          
        } catch (QueryException $e) {
            return [];
        }    
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
    public function getGoods_MaterialOptions()
    {
        $r = Goods_material::all('id','name');

        $returnArr = [];
        foreach ($r as  $sr) {
            $returnArr[$sr['id']] = $sr['name'];
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

    
    public function getAllCategoryOptions()
    {
        $c = Category::all('id','name');

        $returnArr = [];
        foreach ($c as  $sc) {
            $returnArr[$sc['id']] = $sc['name'];
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
   
    public function getCategoryOptions($request)
    {
        $oders_to_supplier_lines = Order_To_Supplier_Line::where('orders_to_supplier_id','=',$request->order_to_supplierId)->get();
        // if (isset($request->order_to_supplierId)) {
        // }
        $returnArr = [];
        foreach($oders_to_supplier_lines as $orders_to_supplier_line){
            // dd($orders_to_supplier_line);
            if(!in_array( $orders_to_supplier_line['category'], $returnArr, true)){
                $returnArr[$orders_to_supplier_line['category']] = $orders_to_supplier_line['category'];
                // array_push($returnArr, $orders_to_supplier_line['category']);
            }
        }
        // dd($returnArr);
        return $returnArr;
    }

}
