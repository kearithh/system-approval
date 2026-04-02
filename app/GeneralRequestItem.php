<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralRequestItem extends Model
{
    protected $table = 'general_request_items';
    protected $fillable = [
        'request_id',
        'name',
        'qty',
        'currency',
        'amount',
        'min_money',
        'current_money',
        'excess_money',
        'no',
        'descrip',
        'debit',
        'credit',
        'currency_exchange',
        'money_exchange',
        'rate',
        'currency_remittance',
        'money_remittance',
    ];
}
