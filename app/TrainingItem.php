<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingItem extends Model
{
    protected $table = 'training_items';
    protected $fillable = [
        'request_id',
        'position',
        'course',
        'from_date',
        'to_date',
        'from_time',
        'to_time',
        'number',
        'location',
        'created_at',
        'updated_at'
    ];

}
