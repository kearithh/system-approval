<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashAdvanceItem extends Model
{

    protected $table = 'cash_advance_items';
    protected $fillable = [
        'request_id',
        'name',
        'desc',
        'purpose',
        'qty',
        'unit',
        'unit_price',
        'currency',
        'remark',
        'date',
    ];
}
