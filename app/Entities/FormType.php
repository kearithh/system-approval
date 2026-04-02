<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'slug',
        'name',
        'desc',
        'order'
    ];
}
