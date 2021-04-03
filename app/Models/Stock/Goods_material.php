<?php

namespace App\Models\Stock;

use App\Models\Stock\Category;
use App\Models\Stock\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Goods_material extends Model
{
    use HasFactory;
    
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class);
    }
}
