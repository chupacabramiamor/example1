<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlanList;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return new PlanList(
            Plan::whereNull('disabled_at')->get()
        );
    }
}
