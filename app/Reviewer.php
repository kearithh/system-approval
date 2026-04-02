<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reviewer extends Model
{
    //protected $fillable = ['name'];
    protected $table = 'review_request';

    protected $fillable = [
        'id',
        'user_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function userReview()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
