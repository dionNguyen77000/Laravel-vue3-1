<?php

namespace App\Models\Stock;

use App\Models\Stock\Goods_material;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'group',
        'representative',
        'phone',
        'address',
        'description',
        'email'
    ];

    public function goods_materials()
    {
        return $this->belongsToMany(Goods_material::class);
    }
}
