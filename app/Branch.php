<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    //protected $fillable = ['name'];
    protected $table = 'branches';

    protected $fillable = [
        'id',
        'code',
        'short_name',
        'name_en',
        'name_km',
        'company_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
