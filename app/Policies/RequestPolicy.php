<?php

namespace App\Policies;

use App\Entities\STSKRequest;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequestPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, STSKRequest $STSKRequest)
    {
        return $user->id === $STSKRequest->user_id;
    }
}
