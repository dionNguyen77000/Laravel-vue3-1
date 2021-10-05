<?php

namespace App\Http\Controllers\DataTable;

use Image;

use App\Models\User;
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
use App\Models\Stock\Invoices_From_Supplier;
use App\Http\Resources\Stock\Invoices_From_SupplierResourceDB;

class Invoices_From_SupplierController extends DataTableController

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
          'invoiceOptions'=> $this->getInvoiceOptions(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]
      ]);
    }

    public function builder()
    {
        return Invoices_From_Supplier::query();
    }

    
    public function getCustomColumnsNames()
    {
        return [
            'img_thumbnail' => 'img',
            'supplier_invoice_number' => 'invoice',
            'orders_to_supplier_id' => 'order',
            'user' => 'checker',
            'invoice_no' => 'invoice',
            'total_price' => 'total',
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id',
            'user',
            'img',
            'img_thumbnail',
            'supplier',
            'supplier_invoice_number',
            'received_date',
            'total_price',
            'orders_to_supplier_id',
            'Note',
            'paid'

        ];
    }
    public function getRetrievedColumns()
    {
        return [
            'id',
            'user',
            'img',
            'img_three',
            'img_two',
            'img_thumbnail',
            'supplier',
            'supplier_invoice_number',
            'received_date',
            'total_price',
            'orders_to_supplier_id',
            'Note',
            'paid',
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            'user',
            'img',
            'img_thumbnail',
            'supplier_invoice_number',
            'received_date',
            'total_price',
            'Note',
            'paid'

        ];
    }

    public function getCreatedColumns()
    {
        return [
            'id',
            'img',
            'img_thumbnail',
            'supplier',
            'supplier_invoice_number',
            'received_date',
            'total_price',
            'orders_to_supplier_id',
            'Note',
            'paid',
        ];
    }
    
    public function store(Request $request)

    {
        $this->validate($request, [
            'id',
            'img',
            'img_thumbnail',
            'supplier',
            'supplier_invoice_number',
            'received_date',
            'total_price',
            'orders_to_supplier_id',
        ]);

        
        $order_to_supplier = $this->builder->create
        (           
            $request->only($this->getCreatedColumns())        
        );

        return $order_to_supplier;
      
    }

    public function update($id, Request $request)
    {
       
        $this->validate($request, [
            // 'total_price' => 'required',
            'total_price' => 'numeric|nullable'   
        ]);

        // $currencies = new ISOCurrencies();
        // $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        // $moneyParser = new IntlMoneyParser($numberFormatter, $currencies);
        // $money = $moneyParser->parse($request->input('price'));
        
        // dd ($money->getAmount()); // outputs 100

        $invoice=  $this->builder->find($id);
        
        $updatedSuccess = $invoice->update(
        
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

        if (isset($request->user)) {
            $builder =   $builder->where('user','=',$request->user);
        }
        if (isset($request->supplier)) {
            $builder =   $builder->where('supplier','=',$request->supplier);
        }
      
        // dd($builder);

        try {
            return Invoices_From_SupplierResourceDB::collection(
                $builder
                ->limit($request->limit)
                ->orderBy('id', 'asc')
                ->get($this->getRetrievedColumns())
            );
            
          
        } catch (QueryException $e) {
            return [];
        }    
    }

    public function saveImage($invoiceAndsilderImageid, Request $request)
    {
        $invoiceAndsilderImageidArray = explode('-',$invoiceAndsilderImageid);

        // dd($invoiceAndsilderImageidArray);
       
        
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
        // $thumbnailWithoutExtension = pathinfo($originName,PATHINFO_FILENAME);

        $dateInBrisbane= Carbon::now('Australia/Brisbane');
        $receivedDate=$dateInBrisbane->isoFormat('MMMDoYY'); 
        
        
        $the_invoice_from_supplier = $this->builder->find($invoiceAndsilderImageidArray[0]);

        $the_order_to_supplier = $the_invoice_from_supplier->orders_to_supplier;
        
        $supplierName=$the_order_to_supplier->supplier;

      
        // deal with second or third invoice image
        if(count($invoiceAndsilderImageidArray)>1 ){
            $invoice_img_number = $invoiceAndsilderImageidArray[1];

            $fileName="Invoice_$supplierName"."_"."$receivedDate";
            $imageName = $fileName.'_' .$invoice_img_number.'_jpg'.$originName;

            Storage::put('public/invoices_from_supplier/'. $imageName, $imageNameResize->__toString());

            //delete old image
            if ($invoice_img_number == 2) {
                if ($the_invoice_from_supplier->img_two){
                    $result_image_array = explode('/',$the_invoice_from_supplier->img_two);
                    $old_image_name = $result_image_array[count($result_image_array)-1];
                    Storage::delete([
                    'public/invoices_from_supplier/'. $old_image_name,
                    ]);
                }
                // save new image path to database
                $the_invoice_from_supplier -> img_two = "/storage/invoices_from_supplier/".$imageName;
                $the_invoice_from_supplier -> save();
            } else if ($invoice_img_number == 3) {
                if ($the_invoice_from_supplier->img_three){
                    $result_image_array = explode('/',$the_invoice_from_supplier->img_three);
                    $old_image_name = $result_image_array[count($result_image_array)-1];
                    Storage::delete([
                    'public/invoices_from_supplier/'. $old_image_name,
                    ]);
                }
                $the_invoice_from_supplier -> img_three = "/storage/invoices_from_supplier/".$imageName;
                $the_invoice_from_supplier -> save();
            }
          
        } 
        
        // deal with invoice image 
        else {
            
            $fileName="Invoice_$supplierName"."_"."$receivedDate";
            $imageName = $fileName.'_'.$originName;
            $thumbnailName = $fileName.'_thumbnail_'.$originName;

            Storage::put('public/invoices_from_supplier/'. $imageName, $imageNameResize->__toString());
            Storage::put('public/invoices_from_supplier/'. $thumbnailName, $thumbnailNameResize->__toString());
            //delete old image
            if ($the_invoice_from_supplier->img_thumbnail){
                $result_thumbnail_array = explode('/',$the_invoice_from_supplier->img_thumbnail);
                $old_thumbnail_name = $result_thumbnail_array[count($result_thumbnail_array)-1];
                Storage::delete([
                'public/invoices_from_supplier/'. $old_thumbnail_name,
                ]);
            }
            if ($the_invoice_from_supplier->img){
                $result_image_array = explode('/',$the_invoice_from_supplier->img);
                $old_image_name = $result_image_array[count($result_image_array)-1];
                Storage::delete([
                'public/invoices_from_supplier/'. $old_image_name,
                ]);
            }
            // save new image
            $the_invoice_from_supplier -> img_thumbnail = "/storage/invoices_from_supplier/".$thumbnailName;
            $the_invoice_from_supplier -> img = "/storage/invoices_from_supplier/".$imageName;
            $the_invoice_from_supplier -> save();
        }
        return $the_invoice_from_supplier;
    }

    public function updateNote($id, Request $request){
        $invoice =  $this->builder->find($id);
        $invoice->Note = $request->note;
        $invoice->save();
        return   $invoice;   
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

    public function getInvoiceOptions()
    {
        $c = Invoices_From_Supplier::all('id','supplier_invoice_number');

        $returnArr = [];
        foreach ($c as  $sc) {
            $returnArr[$sc['supplier_invoice_number']] = $sc['supplier_invoice_number'];
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
