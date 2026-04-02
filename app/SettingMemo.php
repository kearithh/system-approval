<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SettingMemo extends Model
{
    protected $table = 'setting_memo';
    protected $fillable = [
    	'company_id',
        'no',
    ];
}
