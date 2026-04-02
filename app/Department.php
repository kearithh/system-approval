<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
    	'short_name',
        'short_name_km',
    	'name_en',
        'name_km',
        'description',
    ];
}
