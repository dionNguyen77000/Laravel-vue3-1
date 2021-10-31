<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity_Log extends Model
{
    protected $table = 'activity_log';
    use HasFactory;
    protected $fillable = [
       'log_name',
       'description',
       'subject_type',
       'subject_id',
       'causer_type',
       'causer_id',
       'properties',
       'created_at',
       'updated_at',
    ];

    
}
