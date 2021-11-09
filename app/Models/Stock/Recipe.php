<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recipe extends Model
{
    protected $table = 'goods_material_intermediate_product';
    use HasFactory;
    protected $fillable = [
        'id',
        'intermediate_product_id',
        'goods_material_id',
        'inter_p_ingredient_id',
        'amount',
        'type',
    ];
    
    
    // public function orders_to_supplier()
    // {
    //     return $this->belongsTo(Orders_To_Supplier::class);
    // }
    // public function goods_material()
    // {
    //     return $this->belongsTo(Goods_material::class);
    // }
}
