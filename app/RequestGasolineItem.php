<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestGasolineItem extends Model
{

    protected $table = 'request_gasoline_items';
    protected $fillable = [
        'request_id',
        'destination',
        'date_start',
        'date_back',
        'unit',
        'start_number',
        'end_number',
        'miles_number',
        'km_number',
        'gasoline_number'
    ];
}
