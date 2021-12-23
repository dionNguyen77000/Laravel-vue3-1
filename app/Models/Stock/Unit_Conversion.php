<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stock\Goods_material;
use App\Models\Stock\Intermediate_product;

class Unit_Conversion extends Model
{
    protected $table = 'unit_conversions';

    use HasFactory;
    protected $fillable = [
        'id',
        'intermediate_product_id',
        'goods_material_id',
        'rate',
        'type',
        'category',
    ];
   
    public function intermediate_product()
    {
        return $this->belongsTo(Intermediate_product::class);
    }
    public function goods_material()
    {
        return $this->belongsTo(Goods_material::class);
    }
}
