<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DamagedLogItem extends Model
{
    protected $table = 'damaged_log_items';
    protected $fillable = [
        'request_id',
        'name',
        'staff',
        'code',
        'number',
        'unit',
        'purchase_date',
        'broken_date',
        'location',
        'created_at',
        'updated_at',
    ];

}
