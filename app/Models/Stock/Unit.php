<?php

namespace App\Models\Stock;

use App\Models\Stock\Goods_material;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
    ];

    public function goods_materials()
    {
        return $this->hasMany(Goods_material::class);
    }
}
