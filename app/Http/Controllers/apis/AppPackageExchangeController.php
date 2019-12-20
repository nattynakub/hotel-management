<?php

namespace App\Http\Controllers\apis;

use App\Http\Helper\MimeCheckRules;
use App\Model\CodeManager;
use App\Model\GeneralSetting;
use App\Model\User;
use App\Model\AppToken;
use App\Model\PackageMaster;
use App\Model\PackagePeriod;
use App\Model\PackageMember;
use App\Model\PackageMemberDetail;
use App\Model\PackageExchange;
use App\Model\AvailableSlot;
use App\Model\CouponMaster;
use App\Model\AppliedCouponCode;
use App\Model\BlogCategory;
use App\Model\BlogPost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\HttpResource;
use App\Http\Resources\HeaderResource;
use Image;

class AppPackageExchangeController extends Controller
{
    private $packageMemberDetails;
    private $packageExchanges;
    private $couponMasters;
    public $_appId;
    public $_userId;

    public function __construct(PackageMemberDetail $packageMemberDetail,
                                PackageExchange $packageExchange,
                                CouponMaster $couponMaster)
    {
        $this->packageMemberDetails = $packageMemberDetail;
        $this->packageExchanges = $packageExchange;
        $this->couponMasters = $couponMaster;
    }

    public function index(){
        return '200';
    }

    public function postPackageMemberExchange(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $msg = '';
        $status_code = '404';
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $owner_package_member_detail_id = (isset($arrayRequests['packageMemberDetailID']))? $arrayRequests['packageMemberDetailID']:0;
        // $owner_package_member_detail_id = (isset($arrayRequests['ownerPmdId']))? $arrayRequests['ownerPmdId']:0;
        // $receiver_package_member_detail_id = (isset($arrayRequests['receiverPmdId']))? $arrayRequests['receiverPmdId']:0;
        $type = "POST";
        $exchange_code = "EX";
        $chk = $this->packageExchanges->where('owner_id', $this->_userId)->where('owner_pmd_id', $owner_package_member_detail_id)->whereNull('deleted_at')->count();
        if($chk == 0){
            $maxExc = $this->packageExchanges->max('id');
            $exc = "EXNOCODE".$maxExc.'P';
            if($owner_package_member_detail_id != 0){
                $dateM = strtoupper(date("M"));
                $dateD = date("d");
                $dateY = date("y");
                $dateExc = $dateM.$dateY;
                $maxExc += 1;
                $excPad = str_pad($maxExc, 5, '0', STR_PAD_LEFT);
                // $dateExc = $dateY.$dateM.$dateD;
                $exc = $exchange_code.$dateExc.$excPad.'P';
                $exchange_status = $this->exchangeStatus(0);
                $arrayCreate = [
                    'exchange_code' => $exc,
                    'owner_id' => $this->_userId,
                    'owner_pmd_id' => $owner_package_member_detail_id,
                    'type' => $type,
                    'post_date' => date('Y-m-d H:i:s'),
                    'exchange_status' => 0,
                ];
                $msg = "Success";
                $status_code = "200";

                $packageExchange = $this->packageExchanges->create($arrayCreate);
                $packageExchange = json_decode(json_encode($packageExchange));
                // echo "<pre>";
                // print_r($packageExchange);
                // echo "</pre>";
                $response = [
                    'packageExchangeID' => $packageExchange->id,
                    'exchangeCode' => $packageExchange->exchange_code,
                    'packageMemberDetailID' => $packageExchange->owner_pmd_id,
                    'type' => $packageExchange->type,
                    'postDate' => $packageExchange->post_date,
                    'status' => $exchange_status,
                ];
            }
        }else{
            $msg = "Duplicate Package Exchange Detail";
            $status_code = "200";
            $response = ['massage' => $msg];
        }

        return (new HttpResource ($response))
                ->response($msg) 
                ->setStatusCode($status_code);
    }

    public function requestPackageMemberExchange(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $msg = '';
        $status_code = '404';
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        // $owner_package_member_detail_id = (isset($arrayRequests['ownerPmdId']))? $arrayRequests['ownerPmdId']:0;
        $receiver_package_member_detail_id = (isset($arrayRequests['packageMemberDetailID']))? $arrayRequests['packageMemberDetailID']:0;
        // $receiver_package_member_detail_id = (isset($arrayRequests['receiverPmdId']))? $arrayRequests['receiverPmdId']:0;
        $type = "REQUEST";
        $exchange_code = "EX";
        $chk = $this->packageExchanges->where('receiver_id', $this->_userId)->where('receiver_pmd_id', $receiver_package_member_detail_id)->whereNull('deleted_at')->count();
        if($chk == 0){
            $maxExc = $this->packageExchanges->max('id');
            $exc = "EXNOCODE".$maxExc.'R';
            if($receiver_package_member_detail_id != 0){
                $dateM = strtoupper(date("M"));
                $dateD = date("d");
                $dateY = date("y");
                $dateExc = $dateM.$dateY;
                $maxExc += 1;
                $excPad = str_pad($maxExc, 5, '0', STR_PAD_LEFT);
                // $dateExc = $dateY.$dateM.$dateD;
                $exc = $exchange_code.$dateExc.$excPad.'R';
                $exchange_status = $this->exchangeStatus(0);
                $arrayCreate = [
                    'exchange_code' => $exc,
                    'receiver_id' => $this->_userId,
                    'receiver_pmd_id' => $receiver_package_member_detail_id,
                    'type' => $type,
                    'post_date' => date('Y-m-d H:i:s'),
                    'exchange_status' => 0,
                ];
                $msg = "Success";
                $status_code = "200";

                $packageExchange = $this->packageExchanges->create($arrayCreate);
                $packageExchange = json_decode(json_encode($packageExchange));
                // echo "<pre>";
                // print_r($packageExchange);
                // echo "</pre>";
                $response = [
                    'packageExchangeID' => $packageExchange->id,
                    'exchangeCode' => $packageExchange->exchange_code,
                    'packageMemberDetailID' => $packageExchange->receiver_pmd_id,
                    'type' => $packageExchange->type,
                    'postDate' => $packageExchange->post_date,
                    'status' => $exchange_status,
                ];
            }
        }else{
            $msg = "Duplicate Package Exchange Detail";
            $status_code = "200";
            $response = ['massage' => $msg];
        }

        return (new HttpResource ($response))
                ->response($msg) 
                ->setStatusCode($status_code);
    }

    public function requestOnPostPackageMemberExchange(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $msg = '';
        $status_code = '404';
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $package_exchange_id = (isset($arrayRequests['packageExchangeID']))? $arrayRequests['packageExchangeID']:0;
        // $owner_package_member_detail_id = (isset($arrayRequests['ownerPmdId']))? $arrayRequests['ownerPmdId']:0;
        $receiver_package_member_detail_id = (isset($arrayRequests['packageMemberDetailID']))? $arrayRequests['packageMemberDetailID']:0;
        // $receiver_package_member_detail_id = (isset($arrayRequests['receiverPmdId']))? $arrayRequests['receiverPmdId']:0;
        $type = "REQUEST";
        $exchange_code = "EX";
        $chk = $this->packageExchanges->where('id', $package_exchange_id)->whereNull('deleted_at')->get();
        $chkExc = json_decode(json_encode($chk));
        if(count($chkExc) > 0){
            $receiverPMD = $chkExc[0]->receiver_pmd_id;
            $receiverID = $chkExc[0]->receiver_id;
            if($receiver_package_member_detail_id != 0 && $receiverPMD == 0){
                $exStatus = 3;
                $exchange_status = $this->exchangeStatus(3);
                $arrayUpdate = [
                    'receiver_id' => $this->_userId,
                    'receiver_pmd_id' => $receiver_package_member_detail_id,
                    'exchange_date' => date('Y-m-d H:i:s'),
                    'exchange_status' => 3,
                ];
                $msg = "Success";
                $status_code = "200";

                $packageExchange = $this->packageExchanges->where('id', $package_exchange_id)->update($arrayUpdate);
                // echo "<pre>";
                // print_r($packageExchange);
                // echo "</pre>";
                $getPackageExchange = [];
                if($packageExchange){
                    $getPackageExchange = $this->packageExchanges->where('id', $package_exchange_id)->whereNull('deleted_at')->get();
                    $getPackageExchange = json_decode(json_encode($getPackageExchange));
                }
                // echo "<pre>";
                // print_r($getPackageExchange);
                // echo "</pre>";
                $response = [
                    'packageExchangeID' => $getPackageExchange[0]->id,
                    'exchangeCode' => $getPackageExchange[0]->exchange_code,
                    'ownerID' => $getPackageExchange[0]->owner_id,
                    'ownerPackageMemberDetailID' => $getPackageExchange[0]->owner_pmd_id,
                    'packageMemberDetailID' => intval($receiver_package_member_detail_id),
                    'type' => $getPackageExchange[0]->type,
                    'postDate' => $getPackageExchange[0]->post_date,
                    'exchangeDate' => $getPackageExchange[0]->exchange_date,
                    'status' => $exchange_status,
                ];
            }
        }else{
            $msg = "Package Exchange Detail Not Found !";
            $status_code = "200";
            $response = ['massage' => $msg];
        }

        return (new HttpResource ($response))
                ->response($msg) 
                ->setStatusCode($status_code);
    }

    public function approvePackageExchange(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $msg = '';
        $status_code = '404';
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $userId = $this->_userId;
        $package_exchange_id = (isset($arrayRequests['packageExchangeID']))? $arrayRequests['packageExchangeID']:0;
        $owner_package_member_detail_id = (isset($arrayRequests['packageMemberDetailID']))? $arrayRequests['packageMemberDetailID']:0;
        $exchange_code = "EX";
        $chk = $this->packageExchanges->where('owner_id', $this->_userId)->where('id', $package_exchange_id)->where('receiver_id', '!=', '0')->where('receiver_pmd_id', '!=', '0')->where('exchange_status','3')->whereNull('deleted_at')->count();
        if($chk != 0){
            $exStatus = 1;
            $exchange_status = $this->exchangeStatus($exStatus);
            $arrayUpdate = [
                'confirm_date' => date('Y-m-d H:i:s'),
                'exchange_status' => $exStatus,
            ];

            $packageExchange = $this->packageExchanges->where('id', $package_exchange_id)->update($arrayUpdate);
            $getPackageExchange = [];
            if($packageExchange){
                $msg = "Success";
                $status_code = "200";
                $getPackageExchange = $this->packageExchanges->where('id', $package_exchange_id)->get();
                $getPackageExchange = json_decode(json_encode($getPackageExchange));
            }
            // echo "<pre>";
            // print_r($getPackageExchange);
            // echo "</pre>";
            $response = [
                'packageExchangeID' => $getPackageExchange[0]->id,
                'exchangeCode' => $getPackageExchange[0]->exchange_code,
                'ownerId' => $getPackageExchange[0]->owner_id,
                'ownerPackageMemberDetailID' => $getPackageExchange[0]->owner_pmd_id,
                'receiverId' => $getPackageExchange[0]->receiver_id,
                'receiverPackageMemberDetailID' => $getPackageExchange[0]->receiver_pmd_id,
                'type' => $getPackageExchange[0]->type,
                'postDate' => $getPackageExchange[0]->post_date,
                'exchangeDate' => $getPackageExchange[0]->exchange_date,
                'confirmDate' => $getPackageExchange[0]->confirm_date,
                'status' => $exchange_status,
            ];
            
        }else{
            $msg = "Package Exchange Not Found !";
            $status_code = "200";
            $response = ['massage' => $msg];
        }

        return (new HttpResource ($response))
                ->response($msg) 
                ->setStatusCode($status_code);
    }

    public function rejectPackageExchange(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $msg = '';
        $status_code = '404';
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $package_exchange_id = (isset($arrayRequests['packageExchangeID']))? $arrayRequests['packageExchangeID']:0;
        $rejectType = (isset($arrayRequests['rejectType']))? $arrayRequests['rejectType']:0; //rejectType 0=reset,1=delete
        $userId = $this->_userId;
        $type = "POST";
        $exchange_code = "EX";
        $chk = $this->packageExchanges->where('owner_id', $this->_userId)->where('id', $package_exchange_id)->whereNotIn('exchange_status',['1', '9'])->whereNull('deleted_at')->count();
        if($chk != 0){
            if($rejectType){
                $exStatus = 2;
                $exchange_status = $this->exchangeStatus($exStatus);
                $arrayUpdate = [
                    'confirm_date' => date('Y-m-d H:i:s'),
                    'deleted_at' => date('Y-m-d H:i:s'),
                    'exchange_status' => $exStatus,
                ];
            }else{
                $exStatus = 0;
                $exchange_status = $this->exchangeStatus($exStatus);
                $arrayUpdate = [
                    'receiver_id' => 0,
                    'receiver_pmd_id' => 0,
                    'exchange_date' => null,
                    'confirm_date' => null,
                    'exchange_status' => $exStatus,
                ];
            }

            $packageExchange = $this->packageExchanges->where('id', $package_exchange_id)->update($arrayUpdate);
            $getPackageExchange = [];
            $msg = "Success";
            $status_code = "200";
            $getPackageExchange = $this->packageExchanges->where('id', $package_exchange_id)->get();
            $getPackageExchange = json_decode(json_encode($getPackageExchange));
            // echo "<pre>";
            // print_r($getPackageExchange);
            // echo "</pre>";
            $response = [
                'packageExchangeID' => $getPackageExchange[0]->id,
                'exchangeCode' => $getPackageExchange[0]->exchange_code,
                'ownerId' => $getPackageExchange[0]->owner_id,
                'ownerPackageMemberDetailID' => $getPackageExchange[0]->owner_pmd_id,
                'receiverId' => $getPackageExchange[0]->receiver_id,
                'receiverPackageMemberDetailID' => $getPackageExchange[0]->receiver_pmd_id,
                'type' => $getPackageExchange[0]->type,
                'postDate' => $getPackageExchange[0]->post_date,
                'exchangeDate' => $getPackageExchange[0]->exchange_date,
                'confirmDate' => $getPackageExchange[0]->confirm_date,
                'status' => $exchange_status,
            ];

        }else{
            $msg = "Package Exchange Not Found !";
            $status_code = "200";
            $response = ['massage' => $msg];
        }

        return (new HttpResource ($response))
                ->response($msg) 
                ->setStatusCode($status_code);
    }

    public function showPackageExchange(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $packageExchange = [];
        $msg = '';
        $status_code = '404';
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $userId = $this->_userId;
        $owner_package_member_detail_id = (isset($arrayRequests['ownerPmdId']))? $arrayRequests['ownerPmdId']:0;
        $receiver_package_member_detail_id = (isset($arrayRequests['receiverPmdId']))? $arrayRequests['receiverPmdId']:0;
        $type = (isset($arrayRequests['type']))? $arrayRequests['type']:'POST';
        // $type = "POST";
        $exchange_code = "EX";
        $packageExchange = $this->packageExchanges
        ->where('type', $type)
        ->where('exchange_status', '!=', '1')
        ->where(function($q) use ($userId) {
            return $q->where('owner_id', $userId)->orWhere('receiver_id', $userId);
        })
        ->whereNull('deleted_at')->get();
        // $packageExchange = $this->packageExchanges
        // ->leftJoin('package_member_details as pmd', 'package_exchanges.owner_pmd_id', '=', 'pmd.id')
        // ->leftJoin('coupon_masters as cm', 'pmd.coupon_id', '=', 'cm.id')
        // ->select('*')
        // ->where('package_exchanges.type', $type)
        // ->where('package_exchanges.exchange_status', '!=', '1')
        // ->where(function($q) use ($userId) {
        //     return $q->where('package_exchanges.owner_id', $userId)->orWhere('package_exchanges.receiver_id', $userId);
        // })
        // ->whereNull('package_exchanges.deleted_at')->get();
        // echo "<pre>";
        // print_r($packageExchange);
        // echo "</pre>";
        if(count($packageExchange) > 0){
            foreach($packageExchange as $pK => $pV){
                $ownerPDetails = [];
                $receiverPDetails = [];
                $exchange_status = $this->exchangeStatus($pV->exchange_status);
                $ownerPDetail = $this->packageMemberDetails
                                ->leftJoin('coupon_masters as cm', 'package_member_details.coupon_id', '=', 'cm.id')
                                ->select('*')
                                ->where('package_member_details.id', $pV->owner_pmd_id)
                                ->get();
                // echo "<pre>";
                // print_r($ownerPDetail);
                // echo "</pre>";
                $ownerPDetail = json_decode(json_encode($ownerPDetail));
                if(count($ownerPDetail) > 0){
                    $ownerPDetails = [
                        'offerTitle' => $ownerPDetail[0]->offer_title,
                        'description' => $ownerPDetail[0]->description,
                        'periodStartTime' => $ownerPDetail[0]->period_start_time,
                        'periodEndTime' => $ownerPDetail[0]->period_end_time,
                    ];
                }
                $receiverPDetail = $this->packageMemberDetails
                                ->leftJoin('coupon_masters as cm', 'package_member_details.coupon_id', '=', 'cm.id')
                                ->select('*')
                                ->where('package_member_details.id', $pV->receiver_pmd_id)
                                ->get();
                // echo "<pre>";
                // print_r($receiverPDetail);
                // echo "</pre>";
                $receiverPDetail = json_decode(json_encode($receiverPDetail));
                if(count($receiverPDetail) > 0){
                    $receiverPDetails = [
                        'offerTitle' => $receiverPDetail[0]->offer_title,
                        'description' => $receiverPDetail[0]->description,
                        'periodStartTime' => $receiverPDetail[0]->period_start_time,
                        'periodEndTime' => $receiverPDetail[0]->period_end_time,
                    ];
                }
                $response[] = [
                    'packageExchangeID' => $pV->id,
                    'exchangeCode' => $pV->exchange_code,
                    'ownerId' => $pV->owner_id,
                    'ownerPackageMemberDetailID' => $pV->owner_pmd_id,
                    'ownerPackageMemberDetail' => $ownerPDetails,
                    'receiverId' => $pV->receiver_id,
                    'receiverPackageMemberDetailID' => $pV->receiver_pmd_id,
                    'receiverPackageMemberDetail' => $receiverPDetails,
                    'type' => $pV->type,
                    'postDate' => $pV->post_date,
                    'exchangeDate' => $pV->exchange_date,
                    'confirmDate' => $pV->confirm_date,
                    'status' => $exchange_status,
                ];
            }
        }else{
            $msg = "Package Member Not Found !";
            $status_code = "200";
            $response = ['massage' => $msg];
        }

        return (new HttpResource ($response))
                ->response($msg) 
                ->setStatusCode($status_code);
    }

    public function historyPackageExchange(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $packageExchange = [];
        $msg = '';
        $status_code = '404';
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $userId = $this->_userId;
        $owner_package_member_detail_id = (isset($arrayRequests['ownerPmdId']))? $arrayRequests['ownerPmdId']:0;
        $receiver_package_member_detail_id = (isset($arrayRequests['receiverPmdId']))? $arrayRequests['receiverPmdId']:0;
        $type = (isset($arrayRequests['type']))? $arrayRequests['type']:'POST';
        // $type = "POST";
        $exchange_code = "EX";
        $packageExchange = $this->packageExchanges
        ->where('type', $type)
        ->where('exchange_status', '!=', '1')
        ->where(function($q) use ($userId) {
            return $q->where('owner_id', $userId)->orWhere('receiver_id', $userId);
        })
        ->whereNull('deleted_at')->get();
        // echo "<pre>";
        // print_r($packageExchange);
        // echo "</pre>";
        if(count($packageExchange) > 0){
            foreach($packageExchange as $pK => $pV){
                $ownerPDetails = [];
                $receiverPDetails = [];
                $exchange_status = $this->exchangeStatus($pV->exchange_status);
                $ownerPDetail = $this->packageMemberDetails
                                ->leftJoin('coupon_masters as cm', 'package_member_details.coupon_id', '=', 'cm.id')
                                ->select('*')
                                ->where('package_member_details.id', $pV->owner_pmd_id)
                                ->get();
                // echo "<pre>";
                // print_r($ownerPDetail);
                // echo "</pre>";
                $ownerPDetail = json_decode(json_encode($ownerPDetail));
                if(count($ownerPDetail) > 0){
                    $ownerPDetails = [
                        'offerTitle' => $ownerPDetail[0]->offer_title,
                        'description' => $ownerPDetail[0]->description,
                        'periodStartTime' => $ownerPDetail[0]->period_start_time,
                        'periodEndTime' => $ownerPDetail[0]->period_end_time,
                    ];
                }
                $receiverPDetail = $this->packageMemberDetails
                                ->leftJoin('coupon_masters as cm', 'package_member_details.coupon_id', '=', 'cm.id')
                                ->select('*')
                                ->where('package_member_details.id', $pV->receiver_pmd_id)
                                ->get();
                // echo "<pre>";
                // print_r($receiverPDetail);
                // echo "</pre>";
                $receiverPDetail = json_decode(json_encode($receiverPDetail));
                if(count($receiverPDetail) > 0){
                    $receiverPDetails = [
                        'offerTitle' => $receiverPDetail[0]->offer_title,
                        'description' => $receiverPDetail[0]->description,
                        'periodStartTime' => $receiverPDetail[0]->period_start_time,
                        'periodEndTime' => $receiverPDetail[0]->period_end_time,
                    ];
                }
                $response[] = [
                    'packageExchangeID' => $pV->id,
                    'exchangeCode' => $pV->exchange_code,
                    'ownerId' => $pV->owner_id,
                    'ownerPackageMemberDetailID' => $pV->owner_pmd_id,
                    'ownerPackageMemberDetail' => $ownerPDetails,
                    'receiverId' => $pV->receiver_id,
                    'receiverPackageMemberDetailID' => $pV->receiver_pmd_id,
                    'receiverPackageMemberDetail' => $receiverPDetails,
                    'type' => $pV->type,
                    'postDate' => $pV->post_date,
                    'exchangeDate' => $pV->exchange_date,
                    'confirmDate' => $pV->confirm_date,
                    'status' => $exchange_status,
                ];
            }
        }else{
            $msg = "Package Member Not Found !";
            $status_code = "200";
            $response = ['massage' => $msg];
        }

        return (new HttpResource ($response))
                ->response($msg) 
                ->setStatusCode($status_code);
    }

    public function packageExchangesDetail(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $packageExchange = [];
        $msg = '';
        $status_code = '404';
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $userId = $this->_userId;
        $package_exchange_id = (isset($arrayRequests['packageExchangeID']))? $arrayRequests['packageExchangeID']:0;
        $owner_package_member_detail_id = (isset($arrayRequests['ownerPmdId']))? $arrayRequests['ownerPmdId']:0;
        $receiver_package_member_detail_id = (isset($arrayRequests['receiverPmdId']))? $arrayRequests['receiverPmdId']:0;
        $type = (isset($arrayRequests['type']))? $arrayRequests['type']:'POST';
        // $type = "POST";
        $packageExchange = $this->packageExchanges
            ->where('id', $package_exchange_id)
            ->where(function($q) use ($userId) {
                return $q->where('owner_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->whereNull('deleted_at')->get();
        if(count($packageExchange) > 0){
            $packageExchange = json_decode(json_encode($packageExchange));
            // echo "<pre>";
            // print_r($packageExchange);
            // echo "</pre>";
            $exchange_status = $this->exchangeStatus($packageExchange[0]->exchange_status);
            $ownerPDetails = [];
            $receiverPDetails = [];
            $ownerPDetail = $this->packageMemberDetails
                            ->leftJoin('coupon_masters as cm', 'package_member_details.coupon_id', '=', 'cm.id')
                            ->select('*')
                            ->where('package_member_details.id', $packageExchange[0]->owner_pmd_id)
                            ->get();
            // echo "<pre>";
            // print_r($ownerPDetail);
            // echo "</pre>";
            $ownerPDetail = json_decode(json_encode($ownerPDetail));
            if(count($ownerPDetail) > 0){
                $ownerPDetails = [
                    'offerTitle' => $ownerPDetail[0]->offer_title,
                    'description' => $ownerPDetail[0]->description,
                    'periodStartTime' => $ownerPDetail[0]->period_start_time,
                    'periodEndTime' => $ownerPDetail[0]->period_end_time,
                ];
            }
            $receiverPDetail = $this->packageMemberDetails
                            ->leftJoin('coupon_masters as cm', 'package_member_details.coupon_id', '=', 'cm.id')
                            ->select('*')
                            ->where('package_member_details.id', $packageExchange[0]->receiver_pmd_id)
                            ->get();
            // echo "<pre>";
            // print_r($receiverPDetail);
            // echo "</pre>";
            $receiverPDetail = json_decode(json_encode($receiverPDetail));
            if(count($receiverPDetail) > 0){
                $receiverPDetails = [
                    'offerTitle' => $receiverPDetail[0]->offer_title,
                    'description' => $receiverPDetail[0]->description,
                    'periodStartTime' => $receiverPDetail[0]->period_start_time,
                    'periodEndTime' => $receiverPDetail[0]->period_end_time,
                ];
            }
            $response = [ 
                'packageExchangeID' => $packageExchange[0]->id,
                'exchangeCode' => $packageExchange[0]->exchange_code,
                'ownerId' => $packageExchange[0]->owner_id,
                'ownerPackageMemberDetailID' => $packageExchange[0]->owner_pmd_id,
                'ownerPackageMemberDetail' => $ownerPDetails,
                'receiverId' => $packageExchange[0]->receiver_id,
                'receiverPackageMemberDetailID' => $packageExchange[0]->receiver_pmd_id,
                'receiverPackageMemberDetail' => $receiverPDetails,
                'type' => $packageExchange[0]->type,
                'postDate' => $packageExchange[0]->post_date,
                'exchangeDate' => $packageExchange[0]->exchange_date,
                'confirmDate' => $packageExchange[0]->confirm_date,
                'status' => $exchange_status,
            ];
        }else{
            $msg = "Package Exchange Not Found !";
            $status_code = "200";
            $response = ['massage' => $msg];
        }

        return (new HttpResource ($response))
                ->response($msg) 
                ->setStatusCode($status_code);
    }

    public function availablePackageExchange(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $packageExchange = [];
        $msg = '';
        $status_code = '404';
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $userId = $this->_userId;
        $type = (isset($arrayRequests['type']))? $arrayRequests['type']:'POST';
        // $type = "POST";
        $exchange_code = "EX";
        $packageExchange = $this->packageExchanges
        ->where('type', $type)
        ->where('exchange_status', '0')
        ->orderBy('post_date', 'ASC')
        ->whereNull('deleted_at')
        ->get();
        if(count($packageExchange) > 0){
            foreach($packageExchange as $pK => $pV){
                $exchange_status = $this->exchangeStatus($pV->exchange_status);
                $ownerPDetails = [];
                $receiverPDetails = [];
                $ownerPDetail = $this->packageMemberDetails
                                ->leftJoin('coupon_masters as cm', 'package_member_details.coupon_id', '=', 'cm.id')
                                ->select('*')
                                ->where('package_member_details.id', $pV->owner_pmd_id)
                                ->get();
                // echo "<pre>";
                // print_r($ownerPDetail);
                // echo "</pre>";
                $ownerPDetail = json_decode(json_encode($ownerPDetail));
                if(count($ownerPDetail) > 0){
                    $ownerPDetails = [
                        'offerTitle' => $ownerPDetail[0]->offer_title,
                        'description' => $ownerPDetail[0]->description,
                        'periodStartTime' => $ownerPDetail[0]->period_start_time,
                        'periodEndTime' => $ownerPDetail[0]->period_end_time,
                    ];
                }
                $response[] = [
                    'packageExchangeID' => $pV->id,
                    'exchangeCode' => $pV->exchange_code,
                    'ownerId' => $pV->owner_id,
                    'packageMemberDetailID' => $pV->owner_pmd_id,
                    'packageMemberDetail' => $ownerPDetails,
                    // 'receiverId' => $pV->receiver_id,
                    // 'receiverPackageMemberDetailID' => $pV->receiver_pmd_id,
                    // 'type' => $pV->type,
                    'postDate' => $pV->post_date,
                    // 'exchangeDate' => $pV->exchange_date,
                    // 'confirmDate' => $pV->confirm_date,
                    'status' => $exchange_status,
                ];
            }
        }else{
            $msg = "Package Member Not Found !";
            $status_code = "200";
            $response = ['massage' => $msg];
        }

        return (new HttpResource ($response))
                ->response($msg) 
                ->setStatusCode($status_code);
    }

    public function exchangeStatus($status){
        switch($status){
            case '1': $ret = "Approve"; break;
            case '2': $ret = "Reject"; break;
            case '3': $ret = "Request"; break;
            case '9': $ret = "Expired"; break;
            default: $ret = "Wait";
        }
        return $ret;
    }
}
