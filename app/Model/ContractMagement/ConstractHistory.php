<?php

namespace App\Model\ContractMagement;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\ContractMagement\Contracts;
use App\Model\ContractMagement\Properties;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConstractHistory extends Model
{
    use SoftDeletes;
    protected $table = 'contracts_histories';
    protected $fillable = [
        'id',
        'contract_id',
        'property_id',
        'data',
        'deleted_by',
        'updated_by',
        'created_by',
    ];

    public function contract()
    {
        return $this->belongsTo(Contracts::class,'contract_id','id');
    }
    public function userCreated() {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function userUpdateBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function propertyHis()
    {
        return $this->belongsTo(Properties::class, 'property_id');
    }


}
