<?php

namespace App\Services\Traits;

use Illuminate\Foundation\Auth\User as Authenticatable;

trait WithAuthUser {
    /**
     * Авторизированный пользователь
     * @var \Illuminate\Foundation\Auth\User
     */
    private $user;

    public function setAuthUser(Authenticatable $user)
    {
        $this->user = $user;

        return $this;
    }
}