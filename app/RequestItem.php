<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{

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
    ];
}
