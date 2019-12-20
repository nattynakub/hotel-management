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

class AppPrivilegesController extends Controller
{
    public function index(){
        return '200';
    }
    
    public function getReward(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";

        $response = [
            [
                'rewardId' => '1',
                'rewardName' => 'p1',
                'rewardDetail' => 'reward detail 1',
                'rewardPoint' => 1,
                'rewardExpireDate' => '2020-01-01',
                'rewardImage' => [
                    'name' => 'img1',
                    'description' => 'image 1',
                    'imgURL' => 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1024&q=80',
                ]
            ],
            [
                'rewardId' => '2',
                'rewardName' => 'p2',
                'rewardDetail' => 'reward detail 2',
                'rewardPoint' => 1,
                'rewardExpireDate' => '2020-01-01',
                'rewardImage' => [
                    'name' => 'img2',
                    'description' => 'image 2',
                    'imgURL' => 'https://images.unsplash.com/photo-1513885535751-8b9238bd345a?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1050&q=80',
                ]
            ],
        ];

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }

}
