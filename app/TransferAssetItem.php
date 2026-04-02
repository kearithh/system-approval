<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferAssetItem extends Model
{

	protected $table = 'transfer_asset_items';

    protected $fillable = [
        'request_id',
        'name',
        'staff',
        'position',
        'detail',
        'from',
        'to',
        'other',
        'created_at',
        'updated_at',
    ];

}
