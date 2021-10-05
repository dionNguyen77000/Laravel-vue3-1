<?php

namespace App\Models\Stock;

use App\Models\Stock\Orders_To_Supplier;
use App\Models\Stock\Goods_material;
use App\Models\Traits\HasPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order_To_Supplier_Line extends Model
{
    protected $table = 'order_to_supplier_lines';
    use HasFactory;
    protected $fillable = [
        'id',
        'orders_to_supplier_id',
        'goods_material_id',
        'goods_material',
        'unit',
        'o_unit_quantity',
        'o_unit_price',
        'o_line_price',
        'category',
        'invoice_number',
        'i_unit_quantity',
        'i_unit_price',
        'i_line_price',
    ];
    
    
    public function orders_to_supplier()
    {
        return $this->belongsTo(Orders_To_Supplier::class);
    }
    public function goods_material()
    {
        return $this->belongsTo(Goods_material::class);
    }
}
