<?php

namespace App;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillageLoanItem extends Model
{
    protected $table = 'village_loan_items';
    protected $fillable = [
        'request_id',
        'name',
        'cid',
        'amount',
        'name_v',
        'road',
    ];
}

