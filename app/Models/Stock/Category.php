<?php

namespace App\Models\Stock;

use App\Models\Stock\Goods_material;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'parent_id'
    ];
    
    
    function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function goods_materials()
    {
        return $this->hasMany(Goods_material::class);
    }
}
