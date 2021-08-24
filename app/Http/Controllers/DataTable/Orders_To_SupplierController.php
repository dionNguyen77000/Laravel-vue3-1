<?php

namespace App\Http\Controllers\DataTable;

use Image;

use App\Mail\TestMail;
use App\Mail\MyTestMail;


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
            
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id',
            'user_id',
            'supplier_id',
            'invoices_from_supplier_id',
            'ordered_date',
            'estimated_price',
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            'id',
            'user_id',
            'supplier_id',
            'invoices_from_supplier_id',
            'ordered_date',
            'estimated_price',
        ];
    }

    public function getCreatedColumns()
    {
        return [
            'id',
            'user_id',
            'supplier_id',
            'invoices_from_supplier_id',
            'ordered_date',
            'estimated_price',
        ];
    }
    
    public function store(Request $request)

    {
        $this->validate($request, [
            'id',
            'user_id',
            'supplier_id',
            'invoices_from_supplier_id',
            'ordered_date',
            'estimated_price',
        ]);

        
        $order_to_supplier = $this->builder->create
        (
            
            $request->only($this->getCreatedColumns())
            // array_merge($request->only([  'name', 'slug', 'unit_id','supplier_id','category_id', 'description']), 
            //[
            //     'price' => $cart->subtotal()->amount()
            // ])
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
            'user_id',
            'supplier_id',
            'invoices_from_supplier_id',
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
        return $updatedSuccess;
    }

    
    public function show(){
        // Store on default disk
        // $path = 'public/image/goods.xlsx';
        // return Excel::store(new Goods_MaterialExport(2018), $path);
        // return Excel::download(new Goods_MaterialExport, 'goods.xlsx');
        
        // // dd(storage_path('img/logo_backend.png'));
        $details = [
            'email' => 'anhduc.nguyen77000@gmail.com',
            'title' => 'Mail from Golden Lor Yarrabilba',
            'body' => 'This is for testing mail with attachment using gmail'
        ];

        $files = [
            public_path('/storage/image/goods.xlsx')
        ];
        Mail::send('emails.myTestMail', $details, function($message)use($details, $files) {
            $message->to($details["email"], $details["email"])
                    ->subject($details["title"]);
 
            foreach ($files as $file){
                $message->attach($file);
            }
            
        });
        // Mail::to('anhduc.nguyen77000@gmail.com')->send(new MyTestMail($details));

        dd("Email is Sent ok ok.");
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
        if (isset($request->supplier_id)) {
            $builder =   $builder->where('supplier_id','=',$request->supplier_id);
        }
      
        // dd($builder);

        try {
            return Orders_To_SupplierResourceDB::collection(
                $builder
                // ->limit($request->limit)
                ->orderBy('id', 'asc')
                ->get($this->getDisplayableColumns())
            );
            
          
        } catch (QueryException $e) {
            return [];
        }    
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
    
}
