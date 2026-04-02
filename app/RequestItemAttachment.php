<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestItemAttachment extends Model
{
    protected $fillable = ['request_item_id', 'src'];
}
