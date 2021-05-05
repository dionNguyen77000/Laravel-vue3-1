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
    use HasFactory, HasPrice;
    protected $fillable = [
        'name',
        'slug',
        'price',
        'unit_id',
        'supplier_id',
        'category_id',
        'description',
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
}
