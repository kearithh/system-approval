<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Approve extends Model
{
    protected $table = 'approve';

    protected $fillable = [
        'created_by',
        'status',
        'request_id',
        'reviewer_position_id',
        'type',
        'position',
        'reviewer_id',
        'comment',
        'approved_at',
        'comment_attach',
        'user_object'
    ];

    protected $dates = ['approved_at'];
    
    protected $casts = [
        'user_object' => 'object',
    ];

}
