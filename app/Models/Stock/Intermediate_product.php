<?php

namespace App\Models\Stock;

use App\Models\Stock\Unit;
use App\Models\Stock\Category;
use App\Models\Stock\Supplier;
use App\Models\Traits\HasPrice;
use App\Models\Stock\Daily_Emp_Work;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Intermediate_product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
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
        'Preparation'
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
}
