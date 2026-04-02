<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Setting extends Model
{

	protected $table = 'settings';

    protected $fillable = [
        'id',
        'name',
        'value',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'value' => 'array',
    ];
}
