<?php

namespace App\Model\ContractMagement;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\ContractMagement\Properties;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contracts extends Model
{
    use SoftDeletes;
    protected $table = 'contract';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'property_id',
        'data',
        'deleted_by',
        'updated_by',
        'created_by'
    ];
    public function propertiesName() {
        return $this->belongsTo(Properties::class, 'property_id');
    }
    public function userCreated() {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function userUpdateBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
