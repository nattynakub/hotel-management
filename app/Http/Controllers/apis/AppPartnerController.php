<?php

namespace App\Http\Controllers\apis;

use App\Http\Helper\MimeCheckRules;
use App\Model\CodeManager;
use App\Model\GeneralSetting;
use App\Model\User;
use App\Model\AppToken;
use App\Model\Point;
use App\Model\Partner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\HttpResource;
use App\Http\Resources\HeaderResource;

class AppPartnerController extends Controller
{
    public function index(){
        return '200';
    }
    
    public function addPartner(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $partnerName = (isset($arrayRequests['name']))? $arrayRequests['name']:"";
        $partnerDescription = (isset($arrayRequests['description']))? $arrayRequests['description']:"";
        $partnerStatus = (isset($arrayRequests['status']))? $arrayRequests['status']:0;
        $partnerPeriod = (isset($arrayRequests['period']))? $arrayRequests['period']:1;

        $dateN = date('Y-m-d');
        $datePeriod = date_create($dateN);
        $sYearPeriod = date_format($datePeriod, 'Y-m-d 00:00:00');
        date_add($datePeriod, date_interval_create_from_date_string($partnerPeriod.' years'));
        date_sub($datePeriod, date_interval_create_from_date_string('1 days'));
        $eYearPeriod = date_format($datePeriod, 'Y-m-d 23:59:59');
        $arrayRequests['start_date'] = $sYearPeriod;
        $arrayRequests['end_date'] = $eYearPeriod;

        if($partnerName != ""){
            $partnerCreate = Partner::create($arrayRequests);
            $response = $partnerCreate;
        }
        
        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }

    public function editPartner(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $partnerId = (isset($arrayRequests['partnerId']))? $arrayRequests['partnerId']:"";
        $partnerName = (isset($arrayRequests['name']))? $arrayRequests['name']:"";
        $partnerDescription = (isset($arrayRequests['description']))? $arrayRequests['description']:"";
        $partnerStatus = (isset($arrayRequests['status']))? $arrayRequests['status']:0;
        $partnerPeriod = (isset($arrayRequests['period']))? $arrayRequests['period']:1;

        // echo "<pre>";
        // print_r($arrayRequests);
        // echo "</pre>";
        $arrayUpdate = [];
        $dateN = date('Y-m-d');
        $datePeriod = date_create($dateN);
        $sYearPeriod = date_format($datePeriod, 'Y-m-d 00:00:00');
        date_add($datePeriod, date_interval_create_from_date_string($partnerPeriod.' years'));
        date_sub($datePeriod, date_interval_create_from_date_string('1 days'));
        $eYearPeriod = date_format($datePeriod, 'Y-m-d 23:59:59');

        $arrayUpdate['name'] = $partnerName;
        $arrayUpdate['description'] = $partnerDescription;
        $arrayUpdate['partner_status'] = $partnerStatus;
        $arrayUpdate['start_date'] = $sYearPeriod;
        $arrayUpdate['end_date'] = $eYearPeriod;

        if($partnerId != ""){
            $partnerUpdates = Partner::where('id', $partnerId)
            ->update([
                'name' => $partnerName, 
                'description' => $partnerDescription,
                'start_date' => $sYearPeriod,
                'end_date' => $eYearPeriod,
                'partner_status' => $partnerStatus,
            ]);
            if($partnerUpdates){
                $msg = "Update partner success !";
                $response = ['message' => $msg];
                return (new HttpResource ($response))
                    ->response($msg)
                    ->setStatusCode(200);
            }
        }
        
        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }

}
