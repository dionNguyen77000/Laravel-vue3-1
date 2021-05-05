<?php

namespace App\Models\Stock;

use App\Models\Stock\Goods_material;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasChildren;
use App\Models\Traits\IsOrderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasChildren, IsOrderable;
    
    protected $fillable = [
        'name',
        'slug',
        'parent_id'
    ];
    
    public function scopeParentHighest(Builder $builder)
    {
        $builder->whereNull('parent_id');
    }
    public function scopeOrdered(Builder $builder, $direction="asc")
    {
        $builder->orderBy('order',$direction);
    }
    
    function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    function parentCat()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function goods_materials()
    {
        return $this->hasMany(Goods_material::class);
    }
}
