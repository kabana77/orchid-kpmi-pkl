<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BusinessPolicy
{
    public function view(User $user, Business $business): bool
    {
        return $user->hasAccess('platform.businesses') || $this->owns($user, $business);
    }

    public function update(User $user, Business $business): bool
    {
        return $this->view($user, $business);
    }

    public function delete(User $user, Business $business): bool
    {
        return $user->hasAccess('platform.businesses');   // hanya Administrator
    }

    private function owns(User $user, Business $business): bool
    {
        $memberId = $user->member?->id;

        return $memberId !== null && (int) $business->member_id === (int) $memberId;
    }
}
