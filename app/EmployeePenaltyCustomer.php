<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeePenaltyCustomer extends Model
{

    protected $table = 'employee_penalty_customers';

    protected $fillable = [
        'request_id',
        'cus_name',
        'cid',
        'currency',
        'indebted',
        'fraud',
        'system_rincipal',
        'system_rate',
        'system_total',
        'cut_rate',
        'cut_penalty',
        'remark'
    ];
}
