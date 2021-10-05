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
    protected $appends = ['InvNumber'];
    use HasFactory;
   
    protected $fillable = [
        'user',
        'supplier',
        'invoice_number',
        'ordered_date',
        'estimated_price',
        'excel_file',
        'Note',
     ];

     

    // public function getInvNumberAttribute()
    // {
    //     return $this->invoices_from_supplier;
                   
    // }

    public function getInvNumberAttribute()
    {
        return $this->invoices_from_supplier->supplier_invoice_number;
    }
    
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
    //  public function invoices_from_supplier()
    //  {
    //      return $this->belongsTo(Invoices_From_Supplier::class);
    //  }

     public function goods_materials()
     {
         // return $this->belongsToMany(related: Orders_To_Supplier::class, table: 'order_to_supplier_lines', 
         // foreignPivotKey:'goods_material_id', relatedPivotKey:'orders_to_supplier_id');
         return $this->belongsToMany(Goods_material::class,'order_to_supplier_lines', 
        'orders_to_supplier_id', 'goods_material_id');
     }

     public function invoices_from_supplier()
     {
         return $this->hasOne(Invoices_From_Supplier::class,'orders_to_supplier_id');
     }
}
