<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class REDepartment extends Model
{

	protected $table = 're_departments';

    protected $fillable = [
    	'short_name',
    	'name_en',
        'name_km',
        'description',
    ];
}
