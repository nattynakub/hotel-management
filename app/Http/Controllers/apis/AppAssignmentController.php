<?php

namespace App\Http\Controllers\apis;

use App\Http\Helper\MimeCheckRules;
use App\Model\CodeManager;
use App\Model\GeneralSetting;
use App\Model\User;
use App\Model\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\HttpResource;
use App\Http\Resources\HeaderResource;

class AppAssignmentController extends Controller
{
    public function index(){
        return '200';
    }

    public function assignmentPackage(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $packageId = (isset($arrayRequests['packageId']))? $arrayRequests['packageId']:"";
        $transferto = (isset($arrayRequests['transferto']))? $arrayRequests['transferto']:"";
        $status = 1;
        $msg = "Success !!";

        // echo "<pre>";
        // print_r($arrayRequests);
        // echo "</pre>";
        // echo json_encode($arrayRequests);
        
        $response = [
            'userId' => $userId,
            'appId' => $appId,
            'packageId' => $packageId,
            'transferto' => $transferto,
            'status' => $status,
            'message' => $msg,

        ];

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }

}
