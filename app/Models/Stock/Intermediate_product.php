<?php

namespace App\Models\Stock;

use App\Models\Permission;
use App\Models\Stock\Unit;
use App\Models\Stock\Category;
use App\Models\Stock\Supplier;
use App\Models\Traits\HasPrice;
use App\Models\Stock\Daily_Emp_Work;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Intermediate_product extends Model
{
    use HasFactory, LogsActivity;
    protected static $logAttributes = 
    [
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
        'category_id',
        'description',
        'current_qty',
        'prepared_point',
        'coverage',
        'required_qty',
        'permission_id',
        'Active',
        'Preparation',
        'location',
        'location_id',
        
    ];
    
    
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

    public function daily_emp_works()
    {
        return $this->hasMany(Daily_Emp_Work::class);
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
    public function goods_materials()
    {
        return $this->belongsTo(Goods_material::class);
    }
}
