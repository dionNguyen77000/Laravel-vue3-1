<?php

namespace App\Http\Controllers\DataTable;

use Image;

use Money\Currencies\ISOCurrencies;
use Money\Parser\IntlMoneyParser;


use App\Models\Stock\Unit;
use Illuminate\Http\Request;
use App\Models\Stock\Category;
use App\Models\Stock\Supplier;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Stock\Goods_material;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Stock\Goods_MaterialResourceDB;


class Goods_MaterialController extends DataTableController
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
          'unitOptions'=> $this->getUnitOptions(),
          'supplierOptions'=> $this->getSupplierOptions(),
          'categoryOptions'=> $this->getCategoryOptions(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]

      ]);
    }

    public function builder()
    {
        return Goods_material::query();
    }

    
    public function getCustomColumnsNames()
    {
        return [
            'unit_id' => 'Unit',
            'supplier_id' => 'Supplier',
            'category_id' => 'Category',
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id','name', 'thumbnail','slug', 'price',
            'unit_id',
            'supplier_id',
            'category_id',
            'description','image'
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            'name', 'slug', 'thumbnail','price','unit_id','supplier_id','category_id', 'description','image'
        ];
    }

    public function getCreatedColumns()
    {
        return [
            'name', 'slug', 'price','unit_id','supplier_id','category_id', 'description'
        ];
    }
    
    public function store(Request $request)

    {
        $this->validate($request, [
            'name' => 'required|unique:goods_materials,name',
            // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        // if($request->file('image')){

        //     $image = request()->file('image');

        //     $imageName = $image->getClientOriginalName();
    
        //     $imageName = time().'_'.$imageName;
    
        //     $image->move(public_path('/products'),$imageName);
    
        //     $product = $this->builder->find($id);
            
        //     $product -> image = '/good_material_images/' .  $imageName;
            
        //     $product -> save();
        // }
        // else {
        $good_material = $this->builder->create
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
        
        return $good_material;
        // }
        // return "successfully created";
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:goods_materials,name,' . $id
        ]);

        // $currencies = new ISOCurrencies();
        // $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        // $moneyParser = new IntlMoneyParser($numberFormatter, $currencies);
        // $money = $moneyParser->parse($request->input('price'));
        
        // dd ($money->getAmount()); // outputs 100

        $updatedGM =  $this->builder->find($id)->update(
        
            $request->only($this->getUpdatableColumns())
            // array_merge(
            // $request->only([  'name', 'slug', 'unit_id','supplier_id','category_id', 'description'])
            // , 
            // ['price' => $money->getAmount()]
            // )
        
        );

        return $updatedGM;
        // return new PrivateUserResource($user);
    }

    public function saveImage($id, Request $request)
    {
   
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);
        
        $image = request()->file('image');

        $imageNameResize = Image::make($image)
        ->resize(700, null, function ($constraint) {
            $constraint->aspectRatio();
        })
        ->encode('jpg');

        $thumbnailNameResize =  Image::make($image)
        ->fit(200,250)
        ->encode('jpg');

        $originName = $image->getClientOriginalName();
        // $thumbnailWithoutExtension = pathinfo($originName,PATHINFO_FILENAME);
        $imageName = time().'_'.$originName;
        $thumbnailName = time().'_thumbnail_'.$originName;
        Storage::put('public/good_material_images/'. $imageName, $imageNameResize->__toString());
        Storage::put('public/good_material_images/'. $thumbnailName, $thumbnailNameResize->__toString());

        $the_good_material = $this->builder->find($id);

        if ($the_good_material->thumbnail){
            $result_thumbnail_array = explode('/',$the_good_material->thumbnail);
            $old_thumbnail_name = $result_thumbnail_array[count($result_thumbnail_array)-1];
            Storage::delete([
            'public/good_material_images/'. $old_thumbnail_name,
            ]);
        }
        if ($the_good_material->image){
            $result_image_array = explode('/',$the_good_material->image);
            $old_image_name = $result_image_array[count($result_image_array)-1];
            Storage::delete([
            'public/good_material_images/'. $old_image_name,
            ]);
        }
        // save new image
        $the_good_material -> thumbnail = "/storage/good_material_images/".$thumbnailName;
        $the_good_material -> image = "/storage/good_material_images/".$imageName;
        $the_good_material -> save();


        // $thumbnail = $image->getClientOriginalName();
        // $thumbnail = time().'_thumbnail_'.$thumbnail;
        
        // // resize to make thumbnail image
        // Image::make($image)
        // ->fit(200,200)
        // ->save(public_path('/good_material_images/').$thumbnail);

        // calculate md5 hash of encoded image
        // $now = Carbon::now()->toDateTimeString();       
        // $hash = md5($image->__toString().$now);
        // $hash = md5($imageResize->__toString());

        // use hash as a name
        // $path = "good_material_images/{$hash}.jpg";

        // save it locally to ~/public/images/{$hash}.jpg
        // $imageResize->save(public_path($path));

        // $url = "/images/{$hash}.jpg"
        // $url = "/" . $path;


         //upload thumbnail image and resize 
        // ->save(public_path('/good_material_images/').$imageName);


    

        // //delete old image


        // File::delete([
        //     public_path($the_good_material->image),
        //     public_path($the_good_material->thumbnail)
        // ]);

        // $the_good_material -> image = '/good_material_images/' .  $imageName;
        // $the_good_material -> thumbnail = '/good_material_images/' .  $thumbnail;
        // // $the_good_material -> thumbnail = '/good_material_images/' .  $thumbnail;
        
        // $the_good_material -> save();
        return $the_good_material;
        // return "successfully saved";
    }



    protected function getRecords(Request $request)
    {
        $builder = $this->builder;

        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }
     

        if (isset($request->supplier_id)) {
            $builder =   $builder->where('supplier_id','=',$request->supplier_id);
        }

        if (isset($request->category_id)) {
            $builder =   $builder->where('category_id','=',$request->category_id);
        }

        try {
            return Goods_MaterialResourceDB::collection(
                $builder->limit($request->limit)
                ->orderBy('id', 'asc')
                ->get($this->getDisplayableColumns())
            );
            
          
        } catch (QueryException $e) {
            return [];
        }    
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

    public function getSupplierOptions()
    {
        $c = Supplier::all('id','name');

        $returnArr = [];
        foreach ($c as  $sc) {
            $returnArr[$sc['id']] = $sc['name'];
        }
        return $returnArr;
    }

    public function getCategoryOptions()
    {
        $c = Category::all('id','name');

        $returnArr = [];
        foreach ($c as  $sc) {
            $returnArr[$sc['id']] = $sc['name'];
        }
        return $returnArr;
    }

}
