<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PenaltyItem extends Model
{

    protected $table = 'penalty_items';

    protected $fillable = [
        'request_id',
        'name',
        'types',
        'interest_type',
        'desc',
        'currency',
        'amount',
        'amount_collect',
        'percentage',
        'other',
    ];
}
