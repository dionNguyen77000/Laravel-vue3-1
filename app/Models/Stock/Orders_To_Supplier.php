<?php

namespace App\Models\Stock;


use App\Models\User;
use App\Models\Stock\Supplier;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Stock\Invoices_From_Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orders_To_Supplier extends Model
{
    protected $table = 'orders_to_suppliers';
    use HasFactory  ;
    // LogsActivity
    // protected static $logAttributes = ['user', 'supplier','estimated_price','ordered_date','Note'];
    
    // protected $appends = ['InvNumber'];

    protected $fillable = [
       
     ];

     

   
 
   
    
}
