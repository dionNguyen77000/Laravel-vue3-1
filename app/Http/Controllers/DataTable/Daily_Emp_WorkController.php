<?php

namespace App\Http\Controllers\DataTable;

use Image;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Stock\Daily_Emp_Work;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Stock\Intermediate_product;
use App\Http\Resources\Stock\Daily_Emp_WorkResourceDB;
use App\Http\Resources\Stock\Intermediate_ProductResourceDB;

class Daily_Emp_WorkController extends DataTableController
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
          'statusOptions'=> $this->getStatusOptions(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]

      ]);
    }

    public function builder()
    {
        return Daily_Emp_Work::query();
    }

    
    public function getCustomColumnsNames()
    {
        return [
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'date',
            'user_id',
            'intermediate_product_id',
            'doneByEmployee',
            'current_prepared_qty',
            'required_qty',
            'Status',
            'Note'
        ];
    }
    public function getUpdatableColumns()
    {
        return [
            'date',
            'user_id',
            'intermediate_product_id',
            'doneByEmployee',
            'current_prepared_qty',
            'required_qty',
            'Status',
            'Note'
        ];
    }

    public function getCreatedColumns()
    {
        return [
            'date',
            'user_id',
            'intermediate_product_id',
            'doneByEmployee',
            'current_prepared_qty',
            'required_qty',
            'Status',
            'Note'
        ];
    }
    
    public function store(Request $request)

    {
        $this->validate($request, [
          
        ]);

        $newI =  $this->builder->create($request->only($this->getCreatedColumns()));
        if($request->Status != 'Ongoing') {
            if ($request->current_qty <= $request->prepared_point){
                $newI->required_qty = $request->coverage - $request->current_qty;
                $newI->Status = 'Prepare';
                $newI->save();
            } else{
                $newI->required_qty = 0;
                $newI->Status = '';
                $newI->save();
            }
        }
      
       return $newI;
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:daily_emp_works,name,' . $id,
        ]);

        // dd($request->current_qty);
        // $updated_intermediate = $this->builder->find($id);

        // dd($updated_intermediate);
        $intermediate = $this->builder->find($id);
        $updatedSuccess =  $intermediate ->update(
            $request->only($this->getUpdatableColumns())
        );

        if ($updatedSuccess == 1 & $intermediate->current_qty <= $intermediate->prepared_point){
            $intermediate->Status = 'Prepare';
            $intermediate->required_qty = $intermediate->coverage -   $intermediate->current_qty;  
            $intermediate->save();
        } elseif ($updatedSuccess == 1 & $intermediate->current_qty > $intermediate->prepared_point){
            $intermediate->Status = '';
            $intermediate->required_qty = 0;
            $intermediate->save();
        }

        return $updatedSuccess;
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
        Storage::put('public/daily_emp_work_images/'. $imageName, $imageNameResize->__toString());
        Storage::put('public/daily_emp_work_images/'. $thumbnailName, $thumbnailNameResize->__toString());

        $the_daily_emp_work = $this->builder->find($id);

        if ($the_daily_emp_work->thumbnail){
            $result_thumbnail_array = explode('/',$the_daily_emp_work->thumbnail);
            $old_thumbnail_name = $result_thumbnail_array[count($result_thumbnail_array)-1];
            Storage::delete([
            'public/daily_emp_work_images/'. $old_thumbnail_name,
            ]);
        }
        if ($the_daily_emp_work->image){
            $result_image_array = explode('/',$the_daily_emp_work->image);
            $old_image_name = $result_image_array[count($result_image_array)-1];
            Storage::delete([
            'public/daily_emp_work_images/'. $old_image_name,
            ]);
        }
        // save new image
        $the_daily_emp_work -> thumbnail = "/storage/daily_emp_work_images/".$thumbnailName;
        $the_daily_emp_work -> image = "/storage/daily_emp_work_images/".$imageName;
        $the_daily_emp_work -> save();

        return $the_daily_emp_work;
        // return "successfully saved";
    }



    protected function getRecords(Request $request)
    {
        $builder = $this->builder;

        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }

        if (isset($request->category_id)) {
            $builder =   $builder->where('category_id','=',$request->category_id);
        }
        if (isset($request->role_id)) {
            $builder =   $builder->where('role_id','=',$request->role_id);
        }
        if (isset($request->Status)) {
            $builder =   $builder->where('Status','=',$request->Status);
        }

        try {
            return Daily_Emp_WorkResourceDB::collection(
                $builder->limit($request->limit)
                ->orderBy('date', 'asc')
                ->get($this->getDisplayableColumns())
            );
            
          
        } catch (QueryException $e) {
            return [];
        }    
    }


    public function getStatusOptions()
    {
        $returnArr = ['Prepare'];
        return $returnArr;
    }
   

}
