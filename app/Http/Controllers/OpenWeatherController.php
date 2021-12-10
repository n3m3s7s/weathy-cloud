<?php

namespace App\Http\Controllers;

use App\Services\OpenWeatherOneCallApi;
use Illuminate\Http\Request;

class OpenWeatherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function weather(Request $request, OpenWeatherOneCallApi $api)
    {
        return $api->fillWithRequest($request)->toResponse();
    }
}
