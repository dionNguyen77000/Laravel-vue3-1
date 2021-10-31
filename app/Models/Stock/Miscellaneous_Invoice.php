<?php

namespace App\Models\Stock;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Miscellaneous_Invoice extends Model
{
    protected $table = 'miscellaneous_invoices';
    use HasFactory;
    protected $fillable = [
        'user',
        'img',
        'img_thumbnail',
        'img_two',
        'img_three',
        'supplier_invoice_number',
        'received_date',
        'total_price',
        'orders_to_supplier_id',
        'supplier',
        'Note',
        'paid',
        'invoice_category',
        'invoice_type'
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
