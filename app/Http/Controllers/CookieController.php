<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCookieRequest;
use App\Http\Resources\UserCookieItem;
use App\Http\Resources\UserCookieList;
use App\Models\Cookie;
use App\Services\CookieService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CookieController extends Controller
{
    private $cookieSvc;

    public function __construct(CookieService $cookieSvc)
    {
        $this->cookieSvc = $cookieSvc;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new UserCookieList(
            $this->cookieSvc
                ->setAuthUser(Auth::user())
                ->getUserCookies()
                ->load('results.scan.website')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Cookie $cookie)
    {
        $this->authorize('view', $cookie);

        return new UserCookieItem($cookie);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserCookieRequest $request, Cookie $cookie)
    {
        $this->authorize('view', $cookie);

        $cookie = $this->cookieSvc->setAuthUser(Auth::user())->updateUserCookie($cookie, [
            'group_id' => $request->input('group_id'),
            'description' => $request->input('description'),
        ]);

        return new UserCookieItem($cookie);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
