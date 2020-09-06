<?php

namespace App\Policies;

use App\Models\Cookie;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CookiePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the website.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Website  $website
     * @return mixed
     */
    public function view(User $user, Cookie $cookie)
    {
        return in_array($cookie->id, $user->cookies->pluck('id')->toArray());
    }
}
