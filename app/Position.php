<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
    	'short_name',
        'name_km',
        'name_en',
        'level',
        'desc',
    ];
}
