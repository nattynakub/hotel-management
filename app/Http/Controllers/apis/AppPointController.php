<?php

namespace App\Http\Controllers\apis;

use App\Http\Helper\MimeCheckRules;
use App\Model\CodeManager;
use App\Model\GeneralSetting;
use App\Model\User;
use App\Model\AppToken;
use App\Model\Point;
use App\Model\PointTransaction;
use App\Model\WebSetting as WS;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\HttpResource;
use App\Http\Resources\HeaderResource;
use App\Http\Controllers\apis\AppTransactionController as appPointTransaction;

class AppPointController extends Controller
{
    /**
     * @var AppTransactionController
     */
    private $appPointTransaction;

    public  function __construct(AppTransactionController $appPointTransaction)
    {
        $this->appPointTransaction = $appPointTransaction;
    }

    public function index(){
        return '200';
    }

    public function addPoint(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $price = (isset($arrayRequests['price']))? $arrayRequests['price']:"";
        $pointableType = (isset($arrayRequests['pointableType']))? strtoupper($arrayRequests['pointableType']):"ADDITION"; // { SYSTEM, ADDITION }
        $pointableId = (isset($arrayRequests['pointableId'])? $arrayRequests['pointableId']:(isset($arrayRequests['reservationId'])? $arrayRequests['reservationId']:0)); // Is a privileges or reservation id
        $status = 1;
        $flags = '+';
        $message = "";
        $remainPoint = $this->remainPoint($userId);

        $field_name = str_replace('-','_','points').'_'.str_replace('-','_','point_section').'_'.'price';
        $gs = WS::first();
        $priceToPoint = $gs->$field_name;
        $point = floor($price/$priceToPoint);
        if($flags == '+'){
            $current = $remainPoint+$point;
        }else{
            $current = $remainPoint-$point;
        }
        
        $arrayPointTransaction = [
            'user_id' => $userId,
            // 'message' => $message,
            'pointable_id' => $pointableId,
            'pointable_type' => $pointableType,
            'amount' => $point,
            'current' => $current,
            'flags' => $flags,
            'status' => $status,
        ];
        
        $response = $this->appPointTransaction->pointTransaction($arrayPointTransaction);
        // echo "<pre>";
        // print_r($addPoint);
        // echo "</pre>";
        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }

    public function redeemPoint(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $point = (isset($arrayRequests['point']))? $arrayRequests['point']:"";
        $pointableType = (isset($arrayRequests['pointableType']))? strtoupper($arrayRequests['pointableType']):"ADDITION"; // { SYSTEM, ADDITION }
        $pointableId = (isset($arrayRequests['pointableId'])? $arrayRequests['pointableId']:(isset($arrayRequests['reservationId'])? $arrayRequests['reservationId']:0)); // Is a privileges or reservation id
        $status = 1;
        $flags = '-';
        $message = "";
        $remainPoint = $this->remainPoint($userId);

        // $field_name = str_replace('-','_','points').'_'.str_replace('-','_','point_section').'_'.'price';
        // $gs = WS::first();
        // $priceToPoint = $gs->$field_name;
        // $point = floor($price/$priceToPoint);
        if($flags == '+'){
            $current = $remainPoint+$point;
        }else{
            $current = $remainPoint-$point;
        }
        
        $arrayPointTransaction = [
            'user_id' => $userId,
            // 'message' => $message,
            'pointable_id' => $pointableId,
            'pointable_type' => $pointableType,
            'amount' => $point,
            'current' => $current,
            'flags' => $flags,
            'status' => $status,
        ];
        $response = $this->appPointTransaction->pointTransaction($arrayPointTransaction);

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }

    public function remainPoint($userId){
        $getUserPoints = PointTransaction::where('user_id', $userId);
        // $earnPoints = $getUserPoints->where('flags', '+')->where('expire_date', '>', 'NOW()')->sum('amount');
        $earnPoints = $getUserPoints->where('flags', '+')->sum('amount');
        $redeemPoints = $getUserPoints->where('flags', '-')->sum('amount');
        $remainPoints = $earnPoints-$redeemPoints;
        return $remainPoints;
    }
   
}
