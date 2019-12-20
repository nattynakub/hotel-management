<?php

namespace App\Http\Controllers\apis;

use App\Http\Helper\MimeCheckRules;
use App\Model\CodeManager;
use App\Model\GeneralSetting;
use App\Model\User;
use App\Model\AppToken;
use App\Model\Point;
use App\Model\Partner;
use App\Model\PointTransaction;
use App\Model\PrivilegesTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\HttpResource;
use App\Http\Resources\HeaderResource;

class AppTransactionController extends Controller
{
    public function index(){
        return '200';
    }

    public function pointTransaction($transaction){
        $response = [];
        $pointTransaction = PointTransaction::create($transaction);
        if($pointTransaction){
            $response['msg'] = 'Success';
        }else{
            $response['msg'] = 'Error';
        }

        return $response;
    }

    public function privilegesTransaction($transaction){
        $response = [];
        $privilegesTransaction = PrivilegesTransaction::create($transaction);
        if($privilegesTransaction){
            $response['msg'] = 'Success';
        }else{
            $response['msg'] = 'Error';
        }

        return $response;
    }

}
