<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Website;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebsitePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the website.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Website  $website
     * @return mixed
     */
    public function view(User $user, Website $website)
    {
        return $user->type == User::TYPE_SA
             || $user->type == User::TYPE_ADMIN
             || $user->id == $website->subscription->user_id;
    }

    /**
     * Determine whether the user can permanently delete the website.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Website  $website
     * @return mixed
     */
    public function forceDelete(User $user, Website $website)
    {
        //
    }
}
