<?php

namespace App\Model\ContractMagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationChannel extends Model
{
    use SoftDeletes;
    protected $table = 'notification_channels';
    protected $fillable = [
        'id',
        'user_id',
        'data',
        'deleted_by',
        'updated_by',
        'created_by'
    ];
}
