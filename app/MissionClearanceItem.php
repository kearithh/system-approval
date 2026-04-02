<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MissionClearanceItem extends Model
{

    protected $table = 'mission_clearance_items';
    protected $fillable = [
        'request_id',
        'date',
        'branch_name',
        'diet',
        'fees',
        'amount',
        'remark'
    ];
}
