<?php

namespace App\Models\Stock;

use App\Models\Stock\Intermediate_product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Allergy extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function intermediate_products()
    {
        return $this->belongsToMany(Intermediate_product::class);
    }
}
