<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Benefit extends Model
{
    use SoftDeletes;
    protected $table = 'benefit_ot';
    protected $fillable = [
    	'company_id',
        'type',
        'benefit',
        'created_by',
        'updated_by',
    ];
}
