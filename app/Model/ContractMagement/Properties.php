<?php

namespace App\Model\ContractMagement;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\ContractMagement\PropertiesOwner;

class Properties extends Model
{
    use SoftDeletes;
    protected $table = 'properties';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'properties_owner_id',
        'data',
        'deleted_by',
        'updated_by',
        'created_by'
    ];
    public function userCreated() {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function userUpdateBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function proOwner() {
        return $this->belongsTo(PropertiesOwner::class, 'properties_owner_id');
    }


}
