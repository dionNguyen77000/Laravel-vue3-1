<?php

namespace App\Models\Stock;


use App\Models\Stock\Supplier;
use App\Models\Stock\Invoices_From_Supplier;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orders_To_Supplier extends Model
{
    protected $table = 'orders_to_suppliers';
    use HasFactory;
   
    protected $fillable = [
        'user_id',
        'supplier_id',
        'invoices_from_supplier_id',
        'ordered_date',
        'estimated_price',
     ];
    
     public function getRouteKeyName()
     {
         return 'slug';
     }
 
     public function user()
     {
         return $this->belongsTo(User::class);
     }
     public function supplier()
     {
         return $this->belongsTo(Supplier::class);
     }
     public function invoices_from_supplier()
     {
         return $this->belongsTo(Invoices_From_Supplier::class);
     }
}
