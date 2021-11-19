<?php

namespace App\Http\Controllers\DataTable;

use Image;

use App\Models\User;

use App\Mail\MyTestMail;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\Stock\Supplier;
use Illuminate\Support\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Goods_MaterialExport;

use App\Imports\Goods_MaterialImport;
use Illuminate\Support\Facades\Storage;
use App\Models\Stock\Orders_To_Supplier;
use App\Http\Resources\Stock\Miscellaneous_InvoiceResourceDB;
use App\Models\Stock\Miscellaneous_Invoice;

class Miscellaneous_InvoiceController extends DataTableController

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
          'created' => array_values($this->getCreatedColumns()),
          'records' => $this->getRecords($request),
          'custom_columns' => $this->getCustomColumnsNames(),
          'supplierOptions'=> $this->getSupplierOptions(),
          'userOptions'=> $this->getUserOptions(),
        //   'invoiceOptions'=> $this->getInvoiceOptions(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]
      ]);
    }

    public function builder()
    {
        return Miscellaneous_Invoice::query();
    }

    
    public function getCustomColumnsNames()
    {
        return [
            'img_thumbnail' => 'img',
            'supplier_invoice_number' => 'invoice',
            'received_date' => 'date',
            'total_price' => 'total',
            'invoice_category' => 'category',
            'invoice_type' => 'type',
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
            'Note',
            'paid',
            'invoice_category',
            'invoice_type'

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
            'Note',
            'paid',
            'invoice_category',
            'invoice_type'
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            'user',
            'img',
            'img_thumbnail',
            'supplier',
            'supplier_invoice_number',
            'received_date',
            'total_price',
            'Note',
            'paid',
            'invoice_category',
            'invoice_type'

        ];
    }

    public function getCreatedColumns()
    {
        return [
            'user',
            // 'img',
            'img_thumbnail',
            'supplier',
            'supplier_invoice_number',
            'received_date',
            'total_price',
            'Note',
            'paid',
            'invoice_category',
            'invoice_type'
        ];
    }
    
    public function store(Request $request)

    {
        $this->validate($request, [
            'supplier' => 'required',
            'received_date' => 'required',
            'total_price' => 'required|numeric'  
        ]);

        
        $invoice = $this->builder->create
        (           
            $request->only($this->getCreatedColumns())        
        );

        return $invoice;
      
    }

    public function update($id, Request $request)
    {
       
        $this->validate($request, [
            'supplier' => 'required',
            'received_date' => 'required',
            'total_price' => 'required|numeric'   
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
            return Miscellaneous_InvoiceResourceDB::collection(
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
        
        
        $the_invoice = $this->builder->find($invoiceAndsilderImageidArray[0]);

        // $the_order_to_supplier = $the_invoice->orders_to_supplier;
        
        $supplierName=$the_invoice->supplier;

      
        // deal with second or third invoice image
        if(count($invoiceAndsilderImageidArray)>1 ){

          

            $invoice_img_number = $invoiceAndsilderImageidArray[1];
            $fileName="Invoice_$supplierName"."_"."$receivedDate";
            $imageName = $fileName.rand(1,999).'_' .$invoice_img_number.'jpg';


            //delete old image
            if ($invoice_img_number == 2) {
                if ($the_invoice->img_two){
                    $result_image_array = explode('/',$the_invoice->img_two);
                    $old_image_name = $result_image_array[count($result_image_array)-1];
                    Storage::delete([
                    'public/miscellaneous_invoice/'. $old_image_name,
                    ]);
                }
                Storage::put('public/miscellaneous_invoice/'. $imageName, $imageNameResize->__toString());
                // save new image path to database
                // save new image path to database
                $the_invoice -> img_two = "/storage/miscellaneous_invoice/".$imageName;
                $the_invoice -> save();
            } else if ($invoice_img_number == 3) {
                if ($the_invoice->img_three){
                    $result_image_array = explode('/',$the_invoice->img_three);
                    $old_image_name = $result_image_array[count($result_image_array)-1];
                    Storage::delete([
                    'public/miscellaneous_invoice/'. $old_image_name,
                    ]);
                }
                Storage::put('public/miscellaneous_invoice/'. $imageName, $imageNameResize->__toString());
                $the_invoice -> img_three = "/storage/miscellaneous_invoice/".$imageName;
                $the_invoice -> save();
            }
          
        } 
        
        // deal with invoice image 
        else {

            $randomNumber = rand(1,999);            
            $fileName="Invoice_$supplierName"."_"."$receivedDate";
            $imageName = $fileName.$randomNumber.'.jpg';
            $thumbnailName = $fileName.'_thumbnail_'.$randomNumber.'.jpg';

            Storage::put('public/miscellaneous_invoice/'. $imageName, $imageNameResize->__toString());
            Storage::put('public/miscellaneous_invoice/'. $thumbnailName, $thumbnailNameResize->__toString());
            //delete old image
            if ($the_invoice->img_thumbnail){
                $result_thumbnail_array = explode('/',$the_invoice->img_thumbnail);
                $old_thumbnail_name = $result_thumbnail_array[count($result_thumbnail_array)-1];
                Storage::delete([
                'public/miscellaneous_invoice/'. $old_thumbnail_name,
                ]);
            }
            if ($the_invoice->img){
                $result_image_array = explode('/',$the_invoice->img);
                $old_image_name = $result_image_array[count($result_image_array)-1];
                Storage::delete([
                'public/miscellaneous_invoice/'. $old_image_name,
                ]);
            }
            // save new image
            $the_invoice -> img_thumbnail = "/storage/miscellaneous_invoice/".$thumbnailName;
            $the_invoice -> img = "/storage/miscellaneous_invoice/".$imageName;
            $the_invoice -> save();
        }
        return $the_invoice;
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

    // public function getInvoiceOptions()
    // {
    //     $c = Invoices_From_Supplier::all('id','supplier_invoice_number');

    //     $returnArr = [];
    //     foreach ($c as  $sc) {
    //         $returnArr[$sc['supplier_invoice_number']] = $sc['supplier_invoice_number'];
    //     }
    //     return $returnArr;
    // }

    

    
    
}
