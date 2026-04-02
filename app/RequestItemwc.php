<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestItemwc extends Model
{
    protected $table = 'request_items_wc';
    protected $fillable = [
        'request_id',
        'name',
        'type',
        'date',
    ];
}
