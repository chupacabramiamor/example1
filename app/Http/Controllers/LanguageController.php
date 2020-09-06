<?php

namespace App\Http\Controllers;

use App\Services\LngService;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, LngService $lngSvc)
    {
        return $lngSvc->languageList();
    }
}
