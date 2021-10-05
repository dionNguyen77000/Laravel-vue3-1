<?php

namespace App\Models\Stock;

use Money\Money;
use Money\Currency;
use App\Models\Stock\Unit;
use App\Models\Stock\Category;
use App\Models\Stock\Supplier;
use App\Models\Traits\HasPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Goods_material extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'img',
        'img_two',
        'img_three',
        'img_thumbnail',
        'price',
        'unit_id',
        'supplier_id',
        'category_id',
        'description',
        'current_qty',
        'prepared_point',
        'location',
        'coverage',
        'required_qty',
        'permission_id',
        'Active',
        'Preparation'
    ];
    
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function orders_to_suppliers()
    {
        // return $this->belongsToMany(related: Orders_To_Supplier::class, table: 'order_to_supplier_lines', 
        // foreignPivotKey:'goods_material_id', relatedPivotKey:'orders_to_supplier_id');
        return $this->belongsToMany(Orders_To_Supplier::class,'order_to_supplier_lines', 
        'goods_material_id','orders_to_supplier_id');
    }
}
