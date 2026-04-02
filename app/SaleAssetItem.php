<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleAssetItem extends Model
{

    protected $fillable = [
        'request_id',
        'branch',
        'name',
        'asset_tye',
        'code',
        'unit',
        'unit_price',
        'currency',
        'qty',
        'customer',
        'others',
        'attachment',
        'created_at',
        'updated_at',
    ];

}
