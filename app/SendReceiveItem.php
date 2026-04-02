<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SendReceiveItem extends Model
{

    protected $fillable = [
        'request_id',
        'name',
        'code',
        'unit',
        'qty',
        'others',
        'created_at',
        'updated_at',
    ];

}
