<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthUserProfileRequest;
use App\Http\Resources\UserProfileItem;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    private $userSvc;

    public function __construct(UserService $userSvc)
    {
        $this->userSvc = $userSvc;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return new UserProfileItem($this->userSvc->getUserProfile(Auth::id()));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AuthUserProfileRequest $request)
    {
        $user = $this->userSvc->updateUserProfile(Auth::id(), $request->input('fullname'), $request->input('email'));

        if ($request->has('password')) {
            $this->userSvc->setUserPassword($user, $request->input('password'));
        }

        return new UserProfileItem($user);
    }
}
