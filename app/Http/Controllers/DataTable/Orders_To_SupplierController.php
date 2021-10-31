<?php

namespace App\Http\Controllers\DataTable;

use Image;

use App\Mail\TestMail;
use App\Mail\MyTestMail;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\Stock\Supplier;
use Illuminate\Support\Carbon;
use Money\Parser\IntlMoneyParser;
use Money\Currencies\ISOCurrencies;
use App\Http\Controllers\Controller;
use App\Models\Stock\Goods_material;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Goods_MaterialExport;

use App\Imports\Goods_MaterialImport;
use Illuminate\Support\Facades\Storage;
use App\Models\Stock\Orders_To_Supplier;
use App\Http\Resources\Stock\Orders_To_SupplierResourceDB;
use App\Models\Stock\Invoices_From_Supplier;

class Orders_To_SupplierController extends DataTableController

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
          'supplierOptions'=> $this->getSupplierOptions(),
          'userOptions'=> $this->getUserOptions(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]

      ]);
    }

    public function builder()
    {
        return Orders_To_Supplier::query();
    }

    
    public function getCustomColumnsNames()
    {
        return [
            'excel_file' => 'file',
            'InvNumber' => ' Invoice',
            'user' => ' Orderer',
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id',
            'user',
            'supplier',
            'ordered_date',
            'estimated_price',
            'excel_file',
            'Note',
            'paid',
            'InvNumber',
        ];
    }
   
    public function getUpdatableColumns()
    {
        return [
            'user',
            'supplier',
            'ordered_date',
            'estimated_price',
            'excel_file',
            'Note',
            'paid',


        ];
    }

    public function getCreatedColumns()
    {
        return [
            'id',
            'user',
            'supplier',
            'ordered_date',
            'estimated_price',
            'excel_file',
            'Note',
            'paid',

        ];
    }

    public function getRetrievedColumns()
    {
        return [
            'id',
            'user',
            'supplier',
            'ordered_date',
            'estimated_price',
            'excel_file',    
            'Note',
            'paid',

     
        ];
    }
    
    public function store(Request $request)

    {
        $this->validate($request, [
            'id',
            'user',
            'supplier',
            'ordered_date',
            'estimated_price',
            'excel_file',
            'Note',
        ]);

        
        $order_to_supplier = $this->builder->create
        (
            
            $request->only($this->getCreatedColumns())
           
        );

        
        // return $request->user()->orders()->create(
        //     array_merge($request->only(['address_id', 'shipping_method_id', 'payment_method_id']), [
        //         'subtotal' => $cart->subtotal()->amount()
        //     ])
        // );
        
        return $order_to_supplier;
        // }
        // return "successfully created";
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'id',
            'user',
            'supplier',
            'ordered_date',
            'estimated_price',
        ]);

        // $currencies = new ISOCurrencies();
        // $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        // $moneyParser = new IntlMoneyParser($numberFormatter, $currencies);
        // $money = $moneyParser->parse($request->input('price'));
        
        // dd ($money->getAmount()); // outputs 100

        $order =  $this->builder->find($id);
        
        $updatedSuccess = $order->update(
        
            $request->only($this->getUpdatableColumns())
            // array_merge(
            // $request->only([  'name', 'slug', 'unit_id','supplier_id','category_id', 'description'])
            // , 
            // ['price' => $money->getAmount()]
            // )
        
        );

        // if($updatedSuccess==1) {
        //     $relatedInvoice =  $order->invoices_from_supplier;
        //     // dd( $relatedInvoice);
        //     $relatedInvoice->supplier = $order->supplier;
        //     $relatedInvoice->save();
        // }
        return $updatedSuccess;
    }

    
    public function show(){
      
    }
    
    public function updateNote($id, Request $request){
        $order =  $this->builder->find($id);
        $order->Note = $request->note;
        $order->save();
        return   $order;   
    }


   
    
    protected function getRecords(Request $request)
    {
        $builder = $this->builder->with(['invoices_from_supplier']);
        
        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }
     
        if ($this->hasSearchQuery_1($request)) {
            $builder = $this->buildSearch_1($builder, $request);
        }

        if (isset($request->user)) {
            $builder =   $builder->where('user','=',$request->user);
        }
        if (isset($request->supplier)) {
            $builder =   $builder->where('supplier','=',$request->supplier);
        }
        if (isset($request->orders_to_supplierId)) {
            $builder =   $builder->where('id','=',$request->orders_to_supplierId);
        }
      
        // dd($builder);

        try {
            return Orders_To_SupplierResourceDB::collection(
                $builder
                ->limit($request->limit)
                ->orderBy('id', 'asc')
                ->get($this->getRetrievedColumns())
            );
            
          
        } catch (QueryException $e) {
            return [];
        }    
    }

    public function getUserOptions()
    {
        $r = User::all('id','name');

        $returnArr = [];
        foreach ($r as  $sr) {
            $returnArr[$sr['name']] = $sr['name'];
        }
        return $returnArr;
    }


    public function getSupplierOptions()
    {
        $c = Supplier::all('id','name');

        $returnArr = [];
        foreach ($c as  $sc) {
            $returnArr[$sc['name']] = $sc['name'];
        }
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
    public function fileExport(Request $request) 
    {
        return Excel::download(new Goods_MaterialExport, 'goods.xlsx');
    }
     
    /**
    * @return \Illuminate\Support\Collection
    */
    public function fileImport(Request $request) 
    {
        Excel::import(new Goods_MaterialImport,request()->file('file'));
             
        return back();
    }

    
    public function destroy($ids, Request $request)
    {
        if (!$this->allowDeletion) {
            return;
        }

        $arrayIds = explode(',',$ids);

        foreach ($arrayIds  as $id) {
           
            $theOrder = Orders_To_Supplier::find($id);

           
            // cannot delte paid order
            if($theOrder->paid == 'Yes'){
                return 'error-order is already paid';
            } else  $theOrder->delete();
          }

      
        // if (count($arrayIds) > 1 ) {
        //     $this->builder->whereIn('id', explode(',', $ids))->delete();
        // } else if (count($arrayIds) == 1){
        //     $this->builder->find($ids)->delete();
        //     // $this->builder->find($ids)->delete();
        // }
    }
    
}
