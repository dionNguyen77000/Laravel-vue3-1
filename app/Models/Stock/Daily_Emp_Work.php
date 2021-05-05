<?php

namespace App\Models\Stock;

use App\Models\User;
use App\Models\Traits\HasPrice;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stock\Intermediate_product;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Daily_emp_work extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'user_id',
        'intermediate_product_id',
        'doneByEmployee',
        'current_prepared_qty',
        'required_qty',
        'Status',
        'Note'
    ];
    
    
    public function intermediate_product()
    {
        return $this->belongsTo(Intermediate_product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
