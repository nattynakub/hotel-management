<?php

namespace App\Http\Controllers\apis;

use App\Model\CodeManager;
use App\Model\GeneralSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\HttpResource;
use App\Http\Resources\HeaderResource;

class TestController extends Controller
{
    public function index(){
        return '200';
    }

    public function testResponse(){
        // $headerResource = new HeaderResource();
        // $headerBearer = $headerResource->getBearerToken();

        $response = GeneralSetting::first();
        // return (new HttpResource ($response))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return $response;
    }
}
