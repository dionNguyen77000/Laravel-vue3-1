<?php

namespace App\Models\Stock;

use App\Models\Permission;
use Money\Money;
use Money\Currency;
use App\Models\Stock\Unit;
use App\Models\Stock\Category;
use App\Models\Stock\Supplier;
use App\Models\Traits\HasPrice;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Goods_material extends Model
{
    use HasFactory, LogsActivity;
    protected static $logAttributes = [
        'current_qty',
        // 'prepared_point',
        // 'coverage'
    ];
    
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
        'location_id',
        'coverage',
        'required_qty',
        'permission_id',
        'Active',
        'Preparation',
        'O_Status'
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
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
    public function intermediate_products()
    {
        return $this->belongsTo(Intermediate_product::class);
    }
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class)
        ->withPivot('unit_price');
    }

    public function suppliersWithUnputPrice()
    {
        
    }
}
