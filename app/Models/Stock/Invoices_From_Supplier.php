<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices_From_Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_img',
        'supplier_invoice_number',
        'received_date',
        'total_price'
     ];
     
     public function getRouteKeyName()
     {
         return 'slug';
     }
 
}
