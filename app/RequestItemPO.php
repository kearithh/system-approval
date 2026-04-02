<?php

namespace App;
use Carbon\Carbon;
use CollectionHelper;

use Illuminate\Database\Eloquent\Model;

class RequestItemPO extends Model
{
    protected $table = 'request_items_po';

    protected $fillable = [
        'request_id',
        'name',
        'desc',
        'qty',
        'unit_price',
        'currency',
        'vat',
        'amount',
        'ldp',
        'lunit_price',
        'lqty',
        'other',
    ];
}
