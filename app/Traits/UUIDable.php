<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Automatically set Universally Unique Identifier
 *
 * @package App\Traits
 */
trait UUIDable
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function bootUUIDable()
    {
        /**
         * Attach to the 'creating' Model Event to provide a UUID
         * for the `id` field (provided by $model->getKeyName())
         */
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string)Str::uuid();
        });
    }

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }
}
