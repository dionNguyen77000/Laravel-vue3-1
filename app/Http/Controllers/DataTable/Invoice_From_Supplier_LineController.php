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
use App\Models\Stock\Invoice_From_Supplier_Line;
use App\Http\Resources\Stock\Compare_Order_InvoiceResourceDB;
use App\Http\Resources\Stock\Order_To_Supplier_LineResourceDB;
use App\Http\Resources\Stock\Invoice_From_Supplier_LineResourceDB;

class Invoice_From_Supplier_LineController extends DataTableController
{

    protected $allowCreation = true;

    protected $allowDeletion = true;


    public function index(Request $request)
    {
    //   return $this->builder->get();
    // dd($request->compare);
    // if compare the order_to_supplier and invoice_from_supplier
        if($request->compare == 'Yes'){
            return response()->json([
                'data' => [
                'table' => $this->builder->getModel()->getTable(),
                // 'db_column_name' =>array_values($this->getDatabaseColumnNames()),
                'displayable' => array_values($this->getComparedDisplayableColumns()),
                'updatable' => array_values($this->getComparedUpdatableColumns()),
                'records' => $this->getRecords($request),
                'custom_columns' => $this->getComparedCustomColumnsNames(),
                'permissionOptions'=> $this->getPermissionOptions(),
                'roleOptions'=> $this->getRoleOptions(),
                'goods_materialOptions'=> $this->getGoods_MaterialOptions(),
                'supplierOptions'=> $this->getSupplierOptions(),
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
        else {
            return response()->json([
                'data' => [
                'table' => $this->builder->getModel()->getTable(),
                // 'db_column_name' =>array_values($this->getDatabaseColumnNames()),
                'displayable' => array_values($this->getDisplayableColumns()),
                'updatable' => array_values($this->getUpdatableColumns()),
                'records' => $this->getRecords($request),
                'custom_columns' => $this->getCustomColumnsNames(),
                'permissionOptions'=> $this->getPermissionOptions(),
                'roleOptions'=> $this->getRoleOptions(),
                'goods_materialOptions'=> $this->getGoods_MaterialOptions(),
                'supplierOptions'=> $this->getSupplierOptions(),
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
     
    }

    public function builder()
    {
        return Order_To_Supplier_Line::query();
    }

    
    public function getCustomColumnsNames()
    {
        return [
            'i_unit_quantity' => 'quantity',
            'i_unit_price' => 'price/unit',
            'i_line_price' => 'subtotal',
           
        ];
    }
    public function getComparedCustomColumnsNames()
    {
        return [
            'i_unit_quantity' => 'i_qty',
            'i_unit_price' => 'i_unit_price',
            'i_line_price' => 'i_subtotal',
            'o_unit_quantity' => 'o_qty',
            'o_unit_price' => 'o_unit_price',
            'o_line_price' => 'o_subtotal',
           
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id',
            // 'invoices_from_supplier_id',
            'goods_material',
            'i_unit',
            'i_unit_quantity',
            'i_unit_price',
            'i_line_price',
            'category',
        ];
    }
    public function getComparedDisplayableColumns()
    {
        return [
            'id',
            // 'invoices_from_supplier_id',
            'goods_material',
            'o_unit',
            'i_unit',
            'o_unit_quantity',
            'i_unit_quantity',
            'o_unit_price',
            'i_unit_price',
            'o_line_price',
            'i_line_price',
            'category',
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            // 'id',
            // 'invoices_from_supplier_id',
            'goods_material',
            'i_unit',
            'i_unit_quantity',
            'i_unit_price',
            'i_line_price',
            'category',
        ];
    }
    public function getComparedUpdatableColumns()
    {
        return [
            // 'invoices_from_supplier_id',
            'goods_material',
            'i_unit',
            'o_unit',
            'i_unit_quantity',
            'o_unit_quantity',
            'i_unit_price',
            'o_unit_price',
            'i_line_price',
            'o_line_price',
            'category',
        ];
    }

    public function getCreatedColumns()
    {
        return [
            'id',
            'orders_to_supplier_id',
            'goods_material',
            'i_unit',
            'i_unit_quantity',
            'i_unit_price',
            'i_line_price',
            'category',
        ];
    }
    
    public function store(Request $request)

    {
        // dd($request);

        // dd($request->i_unit);
        $this->validate($request, [
            'orders_to_supplier_id' => 'required',
            'goods_material' => 'required',
            'i_unit' => 'required',
            'i_unit_quantity' => 'required|numeric',
            'i_unit_price' => 'required|numeric',
        ]);

        $newD =  $this->builder->create($request->only($this->getCreatedColumns()));

        return $newD;
  
    }

    public function update($id, Request $request)
    {
        // dd($id);
        $this->validate($request, [
            'goods_material' => 'required',
            'i_unit' => 'required',
            'i_unit_quantity' => 'required|numeric',
            'i_unit_price' => 'required|numeric',
        ]);

        // $request->i_line_price = $request->i_unit_quantity * $request->i_unit_price;

        // dd($request->i_line_price);
        // dd( $request->only($this->getUpdatableColumns()));

        $the_invoice_line = Order_To_Supplier_Line::find($id);
                                
        // dd($inter_p);
        $updatedSuccess =  $the_invoice_line ->update(
            $request->only($this->getUpdatableColumns())
        );
        return $updatedSuccess;
    }

    public function destroy($id, Request $request)
    {
        if (!$this->allowDeletion) {
            return;
        }


     
        $invoice_line =  $this->builder->find($id);

        if(is_null($invoice_line->o_unit) || is_null($invoice_line->o_unit_quantity) || is_null($invoice_line->o_unit_price)) {
            $invoice_line->delete();
        // cannot delete the lines already inside order to supplier - instead set 0
        } else return "cannot delete - invoice items existing in order to supplier";



            // $this->builder->find($ids)->delete();
        
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
        //     $builder =   $builder->where('invoices_from_supplier_id','=',$request->order_to_supplierId);
        // }
    
        try {
            if($request->compare == 'Yes'){
                return Compare_Order_InvoiceResourceDB::collection(
                    $builder->limit($request->limit)
                    ->where('orders_to_supplier_id','=',$request->orders_to_supplierId)
                    ->orderBy('created_at', 'asc')
                    ->get($this->getComparedDisplayableColumns())
                );
            } else {
                return Invoice_From_Supplier_LineResourceDB::collection(
                    $builder->limit($request->limit)
                    ->where('orders_to_supplier_id','=',$request->orders_to_supplierId)
                    ->orderBy('created_at', 'asc')
                    ->get($this->getDisplayableColumns())
                );
            }
          
        } catch (QueryException $e) {
            return [];
        }    
    }

    public function addAmountFromInvoiceToStock($ids){
        $arrayIds = explode(',',$ids);       
        $GM_qty_cannot_be_added = [];
        if (count($arrayIds) > 0 ) {
            for ($i=0; $i < count($arrayIds); $i++) { 
                // $invoice_line =  $this->builder->find($arrayIds[$i]);
                $invoice_line = Order_To_Supplier_Line::find($arrayIds[$i]);
                // dd($invoice_line->i_unit_quantity);
               
                $invoice_line_name =  $invoice_line->goods_material;
               
                $GM = Goods_material::where('name',$invoice_line_name)->first();              
                
                if($GM){
                    $unitOfGM = $GM->unit->name;
                    if($unitOfGM == $invoice_line->i_unit)  {
                        $GM->current_qty = $GM->current_qty + $invoice_line->i_unit_quantity;
                        if ($GM->current_qty <= $GM->prepared_point){
                            $GM->Preparation = 'Yes';
                            $GM->required_qty = $GM->coverage -   $GM->current_qty;  
                        } else{
                            $GM->Preparation = 'No';
                            $GM->required_qty = 0;
                        }
                        $GM->O_Status = null;
                        $GM->save();
                    }     
                    else  {
                        array_push($GM_qty_cannot_be_added,$invoice_line);
                    }                           
                } else {
                    array_push($GM_qty_cannot_be_added,$invoice_line);
                }           
            }
        } 
        if(count($GM_qty_cannot_be_added)>0){
            return $GM_qty_cannot_be_added;
        }else return "Added Successfully";
    }
    public function removeAmountFromInvoice($ids){
        $arrayIds = explode(',',$ids);
        
        $GM_qty_cannot_be_added = [];
        if (count($arrayIds) > 0 ) {
            for ($i=0; $i < count($arrayIds); $i++) { 
                // $invoice_line =  $this->builder->find($arrayIds[$i]);
                $invoice_line = Order_To_Supplier_Line::find($arrayIds[$i]);
                // dd($invoice_line->i_unit_quantity);
               
                $invoice_line_name =  $invoice_line->goods_material;
               
                $GM = Goods_material::where('name',$invoice_line_name)->first();    

              
                if($GM){
                    $unitOfGM = $GM->unit->name;
                    if($unitOfGM == $invoice_line->i_unit)  {
                        $GM->current_qty = $GM->current_qty - $invoice_line->i_unit_quantity;
                        if ($GM->current_qty <= $GM->prepared_point){
                            $GM->Preparation = 'Yes';
                            $GM->required_qty = $GM->coverage -   $GM->current_qty;  
                        } else{
                            $GM->Preparation = 'No';
                            $GM->required_qty = 0;
                        }
                        $GM->save();
                    }     
                    else  {
                        array_push($GM_qty_cannot_be_added,$invoice_line);
                    }                           
                } else {
                    array_push($GM_qty_cannot_be_added,$invoice_line);
                }               
            }
        } 
        if(count($GM_qty_cannot_be_added)>0){
            return $GM_qty_cannot_be_added;
        }else return "Deduct Successfully";
    }
    public function removeAmountFromInvoiceAndUpdateOrderStatus($ids){
        $arrayIds = explode(',',$ids);
        
        $GM_qty_cannot_be_added = [];
        if (count($arrayIds) > 0 ) {
            for ($i=0; $i < count($arrayIds); $i++) { 
                // $invoice_line =  $this->builder->find($arrayIds[$i]);
                $invoice_line = Order_To_Supplier_Line::find($arrayIds[$i]);
                // dd($invoice_line->i_unit_quantity);
               
                $invoice_line_name =  $invoice_line->goods_material;
               
                $GM = Goods_material::where('name',$invoice_line_name)->first();    

              
                if($GM){
                    $unitOfGM = $GM->unit->name;
                    if($unitOfGM == $invoice_line->i_unit)  {
                        $GM->current_qty = $GM->current_qty - $invoice_line->i_unit_quantity;
                        if ($GM->current_qty <= $GM->prepared_point){
                            $GM->Preparation = 'Yes';
                            $GM->required_qty = $GM->coverage -   $GM->current_qty;  
                        } else{
                            $GM->Preparation = 'No';
                            $GM->required_qty = 0;
                        }
                        $GM->O_Status = 'waiting';
                        $GM->save();
                    }     
                    else  {
                        array_push($GM_qty_cannot_be_added,$invoice_line);
                    }                           
                } else {
                    array_push($GM_qty_cannot_be_added,$invoice_line);
                }               
            }
        } 
        if(count($GM_qty_cannot_be_added)>0){
            return $GM_qty_cannot_be_added;
        }else return "Deduct Successfully";
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

   
    public function getCategoryOptions($request)
    {
        // $request->orders_to_supplierId
        // dd($request->order_to_supplierId);
        $oders_to_supplier_lines = Order_To_Supplier_Line::where('orders_to_supplier_id','=',$request->orders_to_supplierId)->get();
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

}
