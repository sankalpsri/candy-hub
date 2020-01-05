<?php

namespace GetCandy\Hub\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Cookie\CookieJar;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response;

class DashboardController extends Controller
{
    public function getIndex()
    {
        if (!isset($minutes)) {
            $minutes = 10;
        }
        $response = new Response(view('hub::dashboard'));
        $access = auth()->user()->createToken(env("APP_NAME"))->accessToken;
        $response->withCookie(cookie('access_code', $access, $minutes));
        return $response;
    }
}
