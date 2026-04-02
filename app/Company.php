<?php

namespace App;

use App\Traits\CRUDable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use SoftDeletes;

    use CRUDable;

    protected $table = 'companies';

    protected $casts = [
        'department' => 'collection',
        'letterhead' => 'object'
    ];

    protected $fillable = [
        'id',
        'name',
        'long_name',
        'logo',
        'footer',
        'footer_landscape',
        'type',
        'department',
        'sort',
        'letterhead',
        'created_at',
        'updated_at'
    ];

}
