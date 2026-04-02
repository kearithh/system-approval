<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeePenaltyItem extends Model
{

    protected $table = 'employee_penalty_items';

    protected $fillable = [
        'request_id',
        'name',
        'desc',
        'currency',
        'amount',
        'total',
        'other',
    ];
}
