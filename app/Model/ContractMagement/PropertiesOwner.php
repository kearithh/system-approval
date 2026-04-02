<?php

namespace App\Model\ContractMagement;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertiesOwner extends Model
{
    use SoftDeletes;
    protected $table = 'properties_owner';
    protected $fillable = [
        'id',
        'name',
        'description',
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
}
