<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getUserProfile($userId) : ?User
    {
        return $this->makeUser($userId);
    }

    public function updateUserProfile($userId, string $fullname, string $email) : User
    {
        $user = $this->makeUser($userId);

        $user->fullname = $fullname;
        $user->email = $email;

        $user->save();

        return $user;
    }

    public function setUserPassword($userId, string $password) : User
    {
        $user = $this->makeUser($userId);

        $user->password = $password;
        $user->save();

        return $user;
    }

    public function enableUser($userId)
    {
        $user = $this->makeUser($userId);

        $user->is_enabled = true;
        $user->save();

        return $user;
    }

    public function disableUser($userId)
    {
        $user = $this->makeUser($userId);

        $user->is_enabled = false;
        $user->save();

        return $user;
    }

    private function makeUser($user)
    {
        if (is_int($user)) {
            return User::find($user);
        }

        if ($user instanceof User) {
            return $user;
        }

        return null;
    }
}