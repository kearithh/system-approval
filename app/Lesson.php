<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Lesson extends Model
{
    use SoftDeletes;

    protected $table = 'lesson';

    protected $fillable = [
        'id',
        'title',
        'status',
        'attachment',
        'company_id',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'attachment' => 'object',
        'positions' => 'array',
        'departments' => 'array',
    ];


    public function requester()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function forcompany()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }


}
