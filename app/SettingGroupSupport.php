<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SettingGroupSupport extends Model
{
    protected $table = 'setting_group_support';
    protected $fillable = [
    	'name',
        'value',
        'department',
        'group_department'
    ];
    protected $casts = [
        'value' => 'object',
        'department' => 'object',
        'group_department' => 'object'
    ];

}
