<?php

namespace App\Http\Controllers\apis;

use App\Http\Helper\MimeCheckRules;
use App\Model\CodeManager;
use App\Model\GeneralSetting;
use App\Model\User;
use App\Model\AppToken;
use App\Model\Point;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\HttpResource;
use App\Http\Resources\HeaderResource;

class AppPromotionController extends Controller
{
    public $_appId;
    public $_userId;

    public function index(){
        return '200';
    }
    
    public function getPromotionCode(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";

        $response = [
            [
                'appId' => $this->_appId,
                'userId' => $this->_userId,
                'promotionCode' => 'XYZ'.date('ym').rand(10,99),
                'expireDate' => date("Y-m-d", strtotime(date("Y-m-d")." +10 days")),
            ],
        ];

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }

}
