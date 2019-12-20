<?php

namespace App\Http\Controllers\apis;

use App\Http\Helper\MimeCheckRules;
use App\Model\CodeManager;
use App\Model\GeneralSetting;
use App\Model\User;
use App\Model\AppToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\HttpResource;
use App\Http\Resources\HeaderResource;

class AppProfileController extends Controller
{
    public function index(){
        return '200';
    }

    public function getProfile(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $msg = "";
        $statusCode = 400;
        $arrayRequests = $request->all();
        // $arrayRequests = json_decode($request->getContent(), true);
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";

        // echo "<pre>";
        // print_r($arrayRequests);
        // echo "</pre>";
        // echo $appId;
        // echo "<br />";
        if($userId != ""){
            $getUser = User::where('id', $userId)->get();
            $msg = "Profile Success";
            $statusCode = 200;
            if($getUser){
                $users = new User();
                $users->sex = $getUser[0]->sex;
                $gender = $users->sex();
                $imgUrl = url('core/storage/app/images/passportID/thumbnail');
                $response = [
                    'appId' => $appId,
                    'userId' => $getUser[0]->id,
                    'username' => $getUser[0]->username,
                    'email' => $getUser[0]->email,
                    'fullName' => $getUser[0]->full_name,
                    'phone' => $getUser[0]->phone,
                    'sex' => $gender,
                    'picture' => $imgUrl.'/'.$getUser[0]->picture,
                    'message' => $msg
                ];
            }else{
                $msg = "Profile not found";
                $statusCode = 400;
                $response = ['message' => $msg];
            }
        }else{
            $msg = "Profile not found";
            $statusCode = 400;
            $response = ['message' => $msg];
        }
        // return (new HttpResource ($response))
        //     ->response()
        //     // ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return (new HttpResource ($response))
                ->response($msg)
                ->setStatusCode(200);
    }

    public function getHistory(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();

        $arrayRequests = $request->all();
        // $arrayRequests = json_decode($request->getContent(), true);
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";

        // echo "<pre>";
        // print_r($arrayRequests);
        // echo "</pre>";
        // echo $appId;
        // echo "<br />";
        $imgUrl = url('core/storage/app/images/Packages');

        $response = [];
        $response = [
            [
                'roomId' => '1',
                'roomFloor' => '1',
                'roomNumber' => '101',
                'roomTypeId' => '1',
                'roomTypeCode' => 'r1',
                'roomTypeTitle' => 'room1',
                'roomHigherCapacity' => '3',
                'roomKidsCapacity' => '5',
                'roomPrice' => 1,
                'roomCurrency' => 'USD',
                'roomImage' => [
                    'name' => 'img1',
                    'description' => 'image 1',
                    'imgURL' => $imgUrl."/1566363783.jpg",
                ],
                'roomStatus' => '1', // {0 = Not Avai, 1 = Avai}
            ],
            [
                'roomId' => '2',
                'roomFloor' => '2',
                'roomNumber' => '201',
                'roomTypeId' => '1',
                'roomTypeCode' => 'r1',
                'roomTypeTitle' => 'room1',
                'roomHigherCapacity' => '3',
                'roomKidsCapacity' => '5',
                'roomPrice' => 1,
                'roomCurrency' => 'USD',
                'roomImage' => [
                    'name' => 'img2',
                    'description' => 'image 2',
                    'imgURL' => $imgUrl."/1566363783.jpg",
                ],
                'roomStatus' => '1', // {0 = Not Avai, 1 = Avai}
            ],
        ];

        // return (new HttpResource ($response))
        //     ->response()
        //     // ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }
}
