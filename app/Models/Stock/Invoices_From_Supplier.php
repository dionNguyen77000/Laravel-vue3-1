<?php

namespace App\Models\Stock;


use App\Models\Stock\Orders_To_Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices_From_Supplier extends Model
{
    protected $table = 'invoices_from_suppliers';
    // protected $appends = ['Supplier'];
    // protected $appends = ['EstimatedPrice'];
    use HasFactory;
    protected $fillable = [
        'img',
        'img_three',
        'img_two',
        'img_thumbnail',
        'supplier_invoice_number',
        'received_date',
        'total_price',
        'orders_to_supplier_id',
        'supplier',
        'user',
        'Note',
        'paid',
     ];
     

    // public function getSupplierAttribute()
    // {
    //     return $this->orders_to_supplier;
    // }
    // public function getEstimatedPriceAttribute()
    // {
    //     return $this->orders_to_supplier->estimated_price;
    // }
     public function getRouteKeyName()
     {
         return 'slug';
     }

     public function orders_to_supplier()
     {
         return $this->belongsTo(Orders_To_Supplier::class,'orders_to_supplier_id');
     }
 
}
