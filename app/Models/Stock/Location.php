<?php

namespace App\Models\Stock;

use App\Models\Stock\Good_material;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stock\Intermediate_product;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory,LogsActivity; 

    //only the `deleted` event will get logged automatically
    protected static $recordEvents = ['deleted'];
    protected static $logAttributes = ['id','name'];
    
    protected $fillable = [
        'name', 
        'img',
        'img_two',
        'img_three',
        'img_thumbnail',
    ];

    public function intermediate_products()
    {
        return $this->hasMany(Intermediate_product::class);
    }
    public function good_materials()
    {
        return $this->hasMany(Good_material::class);
    }
}
