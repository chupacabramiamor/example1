<?php

namespace App\Http\Controllers;

use App\Http\Requests\BannerSettingsRequest;
use App\Http\Requests\WebsiteStoringRequest;
use App\Http\Resources\WebsiteItem;
use App\Http\Resources\WebsiteList;
use App\Models\Website;
use App\Services\WebsiteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyWebsiteController extends Controller
{
    private $websiteSvc;

    public function __construct(WebsiteService $websiteSvc)
    {
        $this->websiteSvc = $websiteSvc;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new WebsiteList(
            $this->websiteSvc->setAuthUser(Auth::user())->getList()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WebsiteStoringRequest $request)
    {
        if ($this->websiteSvc->setAuthUser(Auth::user())->isExist($request->input('origin'))) {
            return $this->badRequest(__('incorrect_website_origin'));
        }

        return new WebsiteItem(
            $this->websiteSvc
                ->setAuthUser(Auth::user())
                ->create($request->input('origin'))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Website $website)
    {
        $this->authorize('view', $website);

        return new WebsiteItem($website);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BannerSettingsRequest $request, Website $website)
    {
        $this->authorize('view', $website);

        $website->banner = $request->getContent();
        $website->state = Website::STATE_READY;
        $website->save();

        return new WebsiteItem($website);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Website $website)
    {
        $this->authorize('view', $website);

        $website->delete();

        return $this->ok();
    }

    public function patchComplete(Website $website)
    {
        $this->authorize('view', $website);

        if ($website->state == Website::STATE_NEW) {
            return $this->badRequest(__('unable_this_website_processing'));
        }

        $website->state = Website::STATE_COMPLETED;
        $website->save();

        return new WebsiteItem($website);
    }
}
