<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestItemGRN extends Model
{
    protected $table = 'request_items_grn';

    protected $fillable = [
        'request_id',
        'name',
        'desc',
        'qty',
        'dqty',
        'unit_price',
        'currency',
        'vat',
        'amount',
        'other',
    ];
}
