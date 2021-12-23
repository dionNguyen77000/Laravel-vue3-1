<?php

namespace App\Models;

use App\Models\Stock\Goods_material;
use App\Models\Stock\Intermediate_product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $fillable = [
      'name'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class,'users_permissions');
    }

    public function goods_materials()
    {
        return $this->belongsToMany(Goods_material::class);
    }

    public function goods_materials_checks()
    {
        return $this->hasMany(Goods_material::class,'check_id');
    }
    public function intermediate_products()
    {
        return $this->belongsToMany(Intermediate_product::class);
    }
}
