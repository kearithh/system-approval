<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'card_id',
        'system_user_id',
        'name',
        'username',
        'email',
        'password',
        'password_last_change',
        'email_verified_at',
        'position_id',
        'company_id',
        'branch_id',
        'department_id',
        'gender',
        'user_status',
        'signature',
        'short_signature',
        'avatar',
        'notification_id',
        'delete_at',
        'view_approved_request',
        'edit_pending_request',
        'manage_template_report',
        'role',
        'action_object',
        'role_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'view_approved_request' => 'array',
        'edit_pending_request' => 'array',
        'action_object' => 'object',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
