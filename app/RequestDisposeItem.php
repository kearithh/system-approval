<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestDisposeItem extends Model
{

    protected $fillable = [
        'request_id',
        'name',
        'code',
        'purchase_date',
        'broken_date',
        'location',
        'created_at',
        'updated_at',
    ];

    protected $dates = ['purchase_date', 'broken_date'];
}
