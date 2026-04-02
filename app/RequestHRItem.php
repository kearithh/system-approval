<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestHRItem extends Model
{

    protected $table = 'request_hr_items';
    protected $fillable = [
        'request_id',
        'name',
        'desc',
        'purpose',
        'qty',
        'unit',
        'unit_price',
        'currency',
        'account_no',
        'balance',
        'remark',
        'last_purchase_date',
        'remain_qty',
        'import'
    ];
}
