<?php

namespace App\Models\Stock;

use App\Models\Stock\Orders_To_Supplier;
use App\Models\Stock\Goods_material;
use App\Models\Traits\HasPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice_From_Supplier_Line extends Model
{
    protected $table = 'invoice_from_supplier_lines';
    use HasFactory;
    protected $fillable = [
        'id',
        'invoices_from_supplier_id',
        'goods_material_id',
        'goods_material',
        'o_unit',
        'i_unit',
        'o_unit_quantity',
        'o_unit_price',
        'o_line_price',
        'category'
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
