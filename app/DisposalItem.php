<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DisposalItem extends Model
{

    protected $fillable = [
        'request_id',
        'company_name',
        'name',
        'asset_tye',
        'code',
        'model',
        'purchase_date',
        'broken_date',
        'attachment',
        'qty',
        'desc',
        'created_at',
        'updated_at',
    ];

    protected $dates = ['purchase_date', 'broken_date'];
}
