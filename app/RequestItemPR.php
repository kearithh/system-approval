<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestItemPR extends Model
{

    protected $table = 'request_items_pr';
    protected $fillable = [
        'request_id',
        'name',
        'desc',
        'qty',
        'unit_price',
        'currency',
        'vat',
        'amount',
        'other',
        'attachment',
        'att_name',
        'ldp',
        'lunit_price',
        'lqty'
    ];
}
