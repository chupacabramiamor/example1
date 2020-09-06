<?php

namespace App\Http\Controllers\API\Scanner;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResultRequest;
use App\Http\Resources\CreatedItem;
use App\Http\Resources\ResultItem;
use App\Services\CookieService;
use App\Services\ScanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ResultRequest $request, CookieService $cookieSvc, ScanService $scanSvc)
    {
        if (Auth::user()) {
            $cookieSvc->setAuthUser(Auth::user());
        }

        foreach ($request->input('cookies') as $item) {
            $cookieSvc->createResult(
                $request->input('url'),
                $request->input('scan_key'),
                $item['name'],
                $item['provider'],
                $item['expired_at'],
                $item['path'] ?? null,
                [ 'httpOnly' => $item['httpOnly'], 'secure' => $item['secure'], 'session' => $item['session'], 'sameSite' => $item['sameSite'], 'priority' => $item['priority'] ]
            );
        }

        $stat = new \stdClass;

        $stat->scan_key = $request->input('scan_key');
        $stat->pagesCount = $scanSvc->calcPagesByScanKey($request->input('scan_key'));
        $stat->cookieList = $scanSvc->getCookiesByScanKey($request->input('scan_key'));

        \App\Events\PageScanned::dispatch($stat);

        return new CreatedItem();
    }

    public function determineWebsite(string $origin)
    {

    }
}
