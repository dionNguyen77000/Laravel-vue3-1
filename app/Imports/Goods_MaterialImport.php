<?php

namespace App\Imports;

use App\Models\Stock\Goods_material;
use Maatwebsite\Excel\Concerns\ToModel;

class Goods_MaterialImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Goods_material([
            'name' => $row[0] ,
            'slug' => $row[1],
            'price'=> $row[2],
        ]);
    }
}
