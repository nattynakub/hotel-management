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

class AppPackageController extends Controller
{
    private $posts_cat_id = 1; //Promotions
    public $_appId;
    public $_userId;

    public function index()
    {
        return 'AppPackageController';
    }

    public function addPackage(Request $request)
    {
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $package_code = (isset($arrayRequests['package_code']))? $arrayRequests['package_code']:"";
        $package_name = (isset($arrayRequests['package_name']))? $arrayRequests['package_name']:"";
        $package_type = (isset($arrayRequests['package_type']))? $arrayRequests['package_type']:"";
        $package_benefit = (isset($arrayRequests['package_benefit']))? $arrayRequests['package_benefit']:"";
        $package_discount_percentage = (isset($arrayRequests['package_discount_percentage']))? $arrayRequests['package_discount_percentage']:0;
        $package_discount = (isset($arrayRequests['package_discount']))? $arrayRequests['package_discount']:0;
        $package_price = (isset($arrayRequests['package_price']))? $arrayRequests['package_price']:0;
        $package_currency = (isset($arrayRequests['package_currency']))? $arrayRequests['package_currency']:"";
        $package_description = (isset($arrayRequests['package_description']))? $arrayRequests['package_description']:"";
        $package_agreement = (isset($arrayRequests['package_agreement']))? $arrayRequests['package_agreement']:"";
        $package_image = (isset($arrayRequests['package_image']))? $arrayRequests['package_image']:"";

        $chkPackages = PackageMaster::where(function ($q) use ($package_code) {
            if ($package_code != "") {
                return $q->where('package_code', $package_code);
            }
        })->get();
        $chkPackage = json_decode(json_encode($chkPackages));
        if (empty($chkPackage)) {
            if (!empty($package_image)) {
                $img = time().'.'.$package_image->getClientOriginalExtension();
                $image = Image::make($package_image->getRealPath());
                $destinationPath = storage_path('app/images/Packages/');
                $package_image->move($destinationPath, $img);

                $arrayRequests['package_image'] = $img;
            }

            $getPackages = PackageMaster::create($arrayRequests);
            $getPackage = json_decode(json_encode($getPackages));
            if (!empty($getPackage)) {
                $msg = 'Package create success';
                $status_code = 200;
            } else {
                $msg = 'Package create failed';
                $status_code = 400;
            }
        } else {
            $msg = 'Package code is exists';
            $status_code = 400;
        }

        $response = ['message' => $msg];
        // echo "<pre>";
        // print_r($arrayRequests);
        // echo "</pre>";

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return (new HttpResource($response))
                ->response($msg)
                ->setStatusCode($status_code);
    }

    public function getPackage(Request $request)
    {
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";

        $getPackages = PackageMaster::where('package_status', 1)->get();
        $getPackage = json_decode(json_encode($getPackages));

        // echo "<pre>";
        // print_r($getPackage);
        // echo "</pre>";
        if (!empty($getPackage)) {
            $imgUrl = url('core/storage/app/images/Packages');
            foreach ($getPackage as $pK => $pV) {
                $response[] = [
                    'appId' => $appId,
                    'userId' => $userId,
                    'packageId' => $pV->id,
                    'packageCode' => $pV->package_code,
                    'packageName' => $pV->package_name,
                    'packageType' => $pV->package_type,
                    'packageBenefit' => $pV->package_benefit,
                    'packageDiscountPercentage' => $pV->package_discount_percentage,
                    'packageDiscount' => $pV->package_discount,
                    'packagePrice' => $pV->package_price,
                    'packageCurrency' => $pV->package_currency,
                    'packageDetail' => $pV->package_description,
                    // 'packageAgreement' => $pV->package_agreement,
                    'packageImage' => [
                        'name' => $pV->package_code,
                        'description' => $pV->package_name,
                        'imgURL' => $imgUrl."/".$pV->package_image,
                    ]
                ];
            }
        }
        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource($response);
    }

    public function getPackageDetail(Request $request)
    {
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $packageId = (isset($arrayRequests['packageId']))? $arrayRequests['packageId']:"";

        $getPackages = PackageMaster::where('id', $packageId)->where('package_status', 1)->get();
        $getPackage = json_decode(json_encode($getPackages));
        // echo "<pre>";
        // print_r($getPackage);
        // echo "</pre>";
        if (!empty($getPackage)) {
            $imgUrl = url('core/storage/app/images/Packages');
            $response = [
                'appId' => $appId,
                'userId' => $userId,
                'packageId' => $getPackage[0]->id,
                'packageCode' => $getPackage[0]->package_code,
                'packageName' => $getPackage[0]->package_name,
                'packageType' => $getPackage[0]->package_type,
                'packageBenefit' => $getPackage[0]->package_benefit,
                'packageDiscountPercentage' => $getPackage[0]->package_discount_percentage,
                'packageDiscount' => $getPackage[0]->package_discount,
                'packagePrice' => $getPackage[0]->package_price,
                'packageCurrency' => $getPackage[0]->package_currency,
                'packageDetail' => $getPackage[0]->package_description,
                'packageAgreement' => $getPackage[0]->package_agreement,
                'packageImage' => [
                    'name' => $getPackage[0]->package_code,
                    'description' => $getPackage[0]->package_name,
                    'imgURL' => $imgUrl."/".$getPackage[0]->package_image,
                ]
            ];
        }

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource($response);
    }

    public function registerPackage(Request $request)
    {
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $packageId = (isset($arrayRequests['packageId']))? $arrayRequests['packageId']:"";
        $packageStartDate = (isset($arrayRequests['packageStartDate']))? $arrayRequests['packageStartDate']:date('Y-m-d');

        $getPackages = PackageMaster::where('id', $packageId)->where('package_status', 1)->get();
        $getPackage = json_decode(json_encode($getPackages));
        // echo "<pre>";
        // print_r($getPackage);
        // echo "</pre>";
        $getUserPackages = PackageMember::where('user_id', $this->_userId)->where('package_member_status', 1)->get();
        $getUserPackage = json_decode(json_encode($getUserPackages));
        // if(!empty($getPackage) && empty($getUserPackage)){
        if (!empty($getPackage)) {
            $packages = $getPackage[0];
            $packageBenefit = explode(',', $getPackage[0]->package_benefit);
            // echo "<pre>";
            // print_r($packageBenefit);
            // echo "</pre>";
            $packageDiscountPercentage = $getPackage[0]->package_discount_percentage;
            $packageDiscount = $getPackage[0]->package_discount;
            $yearCount = 1;
            $tmpYear = "";
            $yearPeriod = count($packageBenefit);
            $dateN = date('Y-m-d');
            // $dateNP = date('Y-m-d',strtotime($dateN . "+1 days"));
            $datePeriod = date_create($dateN);
            $sYearPeriod = date_format($datePeriod, 'Y-m-d 00:00:00');
            date_add($datePeriod, date_interval_create_from_date_string($yearPeriod.' years'));
            date_sub($datePeriod, date_interval_create_from_date_string('1 days'));
            $eYearPeriod = date_format($datePeriod, 'Y-m-d 23:59:59');
            // echo $sYearPeriod." : ".$eYearPeriod."<br />";
            $createCoupon = [];
            $time = time();
            $stampTime = date('His');
            foreach ($packageBenefit as $bK => $benefitNight) {
                $dateNow = $packageStartDate;
                if ($tmpYear != "") {
                    // $dateNow = $tmpYear;
                    $dateNow = date('Y-m-d', strtotime($tmpYear . "+1 days"));
                }
                $yearStep = $bK+1;
                $yearSteps = ($yearStep==1 ? $yearStep.'st' : ($yearStep==2 ? $yearStep.'nd' : ($yearStep==3 ? $yearStep."rd" : $yearStep."th")));
                $dateY = date_create($dateNow);
                $sYearDate = date_format($dateY, 'Y-m-d 00:00:00');
                date_add($dateY, date_interval_create_from_date_string('1 years'));
                date_sub($dateY, date_interval_create_from_date_string('1 days'));
                $eYearDate = date_format($dateY, 'Y-m-d 23:59:59');
                $tmpYear = $eYearDate;
                // echo $sYearDate." : ".$eYearDate."<br />";
                $tmpDate = "";
                for ($c=0;$c<$benefitNight;$c++) {
                    $dateP = $dateNow;
                    $datePackageCode = date('YmdHis', strtotime($dateNow.$stampTime. "+".$c." days"));
                    if ($tmpDate != "") {
                        // $dateNow = $tmpDate;
                        $dateP = date('Y-m-d', strtotime($tmpDate . "+1 days"));
                    }
                    // echo $datePackageCode."<br />";
                    $dateB = date_create($dateP);
                    $sDate = date_format($dateB, 'Y-m-d 00:00:00');
                    // date_add($dateB, date_interval_create_from_date_string($c.' days'));
                    // date_sub($dateB, date_interval_create_from_date_string('1 days'));
                    $eDate = date_format($dateB, 'Y-m-d 23:59:59');
                    $tmpDate = $eDate;
                    // echo $sDate." : ".$eDate."<br />";
                    $cStep = $c+1;
                    $arrayCreate = [
                        'offer_title' => $getPackage[0]->package_name." ".$yearSteps." year"." (days ".$cStep.")",
                        'description' => $getPackage[0]->package_name." ".$benefitNight." night "." ".$yearSteps." year"." (days ".$cStep.") start ".$sDate."-".$eDate,
                        'image' => $getPackage[0]->package_image,
                        'period_start_time' => $sDate,
                        'period_end_time' => $eDate,
                        'code' => $getPackage[0]->package_code."_".$benefitNight."_".$yearSteps."y"."_d".$cStep."_".$this->_userId."_".$datePackageCode,
                        'type' => "PERCENTAGE",
                        'value' => 100,
                        'min_amount' => 0,
                        'max_amount' => 500000,
                        'limit_per_user' => 0,
                        'limit_per_coupon' => 0,
                        'status' => 1,
                    ];
                    $createCoupon[] = CouponMaster::create($arrayCreate);
                }
            }
            // echo "<pre>";
            // print_r($createCoupon);
            // echo "</pre>";
            if (!empty($createCoupon)) {
                $arrayCreatePackageMember = [
                    'user_id' => $this->_userId,
                    'package_id' => $getPackage[0]->id,
                    'package_start_time' => $sYearPeriod,
                    'package_end_time' => $eYearPeriod,
                    'package_member_status' => 0,
                ];
                // echo "<pre>";
                // print_r($arrayCreatePackageMember);
                // echo "</pre>";
                $createPackageMember = PackageMember::create($arrayCreatePackageMember);
                $packageMemberId = $createPackageMember->id;
                foreach ($createCoupon as $cK => $coupons) {
                    $arrayPackageMemberDetail = [
                        'package_member_id' => $packageMemberId,
                        'coupon_id' => $coupons->id,
                        'package_coupon' => 'FREENIGHT',
                        'status' => 1,
                    ];
                    $createPackageMemberDetail[] = PackageMemberDetail::create($arrayPackageMemberDetail);
                }
                // echo "<pre>";
                // print_r($createPackageMemberDetail);
                // echo "</pre>";
                // Create Coupon % off vip
                if ($packageDiscountPercentage > 0) {
                    $arrayCreateOff = [
                        'offer_title' => $getPackage[0]->package_name." (".$packageDiscountPercentage."% off)",
                        'description' => $getPackage[0]->package_name." ".$packageDiscountPercentage."% off start ".$sYearPeriod."-".$eYearPeriod,
                        'image' => $getPackage[0]->package_image,
                        'period_start_time' => $sYearPeriod,
                        'period_end_time' => $eYearPeriod,
                        'code' => $getPackage[0]->package_code."_".$packageDiscountPercentage."_".$this->_userId."_".$time,
                        'type' => "PERCENTAGE",
                        'value' => $packageDiscountPercentage,
                        'min_amount' => 0,
                        'max_amount' => 500000,
                        'limit_per_user' => 0,
                        'limit_per_coupon' => 0,
                        'status' => 1,
                    ];
                    $createCouponOff = CouponMaster::create($arrayCreateOff);

                    $arrayPackageMemberOff = [
                        'package_member_id' => $createPackageMember->id,
                        'coupon_id' => $createCouponOff->id,
                        'package_coupon' => 'ADDITIONAL',
                        'status' => 1,
                    ];
                    $createPackageMemberDetailOff = PackageMemberDetail::create($arrayPackageMemberOff);
                }
                if ($packageDiscount > 0) {
                    $arrayCreateDiscount = [
                        'offer_title' => $getPackage[0]->package_name." (".$packageDiscount.$getPackage[0]->package_currency.")",
                        'description' => $getPackage[0]->package_name." ".$packageDiscount.$getPackage[0]->package_currency." start ".$sYearPeriod."-".$eYearPeriod,
                        'image' => $getPackage[0]->package_image,
                        'period_start_time' => $sYearPeriod,
                        'period_end_time' => $eYearPeriod,
                        'code' => $getPackage[0]->package_code."_".$packageDiscount."_".$this->_userId."_".$time,
                        'type' => "FIXED",
                        'value' => $packageDiscount,
                        'min_amount' => 0,
                        'max_amount' => 500000,
                        'limit_per_user' => 0,
                        'limit_per_coupon' => 0,
                        'status' => 1,
                    ];

                    $createCouponDiscount = CouponMaster::create($arrayCreateDiscount);

                    $arrayPackageMemberDiscount = [
                        'package_member_id' => $createPackageMember->id,
                        'coupon_id' => $createCouponDiscount->id,
                        'package_coupon' => 'ADDITIONAL',
                        'status' => 1,
                    ];
                    $createPackageMemberDetailDiscount = PackageMemberDetail::create($arrayPackageMemberDiscount);
                }

                // Create Package Period By coupon id
                $memberPackage = "";
                if (!empty($createPackageMemberDetail)) {
                    foreach ($createPackageMemberDetail as $vC) {
                        $couponMembers[] = $vC->id;
                    }
                    $memberPackage = implode(',', $couponMembers);
                    if (!empty($createPackageMemberDetailOff)) {
                        $memberPackage .= ','.$createPackageMemberDetailOff->id;
                    }
                    if (!empty($createPackageMemberDetailDiscount)) {
                        $memberPackage .= ','.$createPackageMemberDetailDiscount->id;
                    }
                    //update Package member
                    $updatePackageMember = PackageMember::where('id', $packageMemberId)->update(['package_member' => $memberPackage]);
                }
            }

            if (!empty($updatePackageMember)) {
                // Update VIP after payment success
                // $updateVIP = User::where('id', $userId)->update(['vip' => 1]);
            }

            // After Register Package send payment to Mobile
            // $response = [
            //     'appId' => $appId,
            //     'userId' => $userId,
            //     'packageId' => $getPackage[0]->id,
            //     'packageCode' => $getPackage[0]->package_code,
            //     'packageName' => $getPackage[0]->package_name,
            //     'packageType' => $getPackage[0]->package_type,
            //     'packageBenefit' => $getPackage[0]->package_benefit,
            //     'packagePrice' => $getPackage[0]->package_price,
            //     'packageCurrency' => $getPackage[0]->package_currency,
            //     'packageDetail' => $getPackage[0]->package_description,
            //     'packageAgreement' => $getPackage[0]->package_agreement,
            //     'packageImage' => [
            //         'name' => $getPackage[0]->package_code,
            //         'description' => $getPackage[0]->package_name,
            //         'imgURL' => 'http://www.freeimageslive.com/galleries/nature/abstract/pics/snow_surface.jpg',
            //     ]
            // ];
        } else {
            if (!empty($getUserPackage)) {
                $msg = "Package already";
                $response = ['message' => $msg];
                return (new HttpResource($response))
                    ->response($msg)
                    ->setStatusCode(400);
            }
        }

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource($response);
    }

    public function registerPackageNew(Request $request)
    {
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $packageId = (isset($arrayRequests['packageId']))? $arrayRequests['packageId']:"";
        $slotCode = (isset($arrayRequests['slotCode']))? $arrayRequests['slotCode']:"";
        $slotWeek = (isset($arrayRequests['slotWeek']))? $arrayRequests['slotWeek']:"";
        $packageStartDate = (isset($arrayRequests['packageStartDate']))? $arrayRequests['packageStartDate']:date('Y-m-d');

        $getPackages = PackageMaster::where('id', $packageId)->where('package_status', 1)->get();
        $getPackage = json_decode(json_encode($getPackages));
        $getSlots = AvailableSlot::where('slot_code', $slotCode)->where('slot_status', 1)->get();
        $getSlot = json_decode(json_encode($getSlots));
        // echo "<pre>";
        // print_r($getSlot);
        // echo "</pre>";
        $chkSlotUsage = 0;
        // $getUserPackages = PackageMember::where('user_id', $userId)->where('package_member_status', 1)->get();
        // $getUserPackage = json_decode(json_encode($getUserPackages));
        // if(!empty($getPackage) && empty($getUserPackage)){
        if (!empty($getPackage) && !empty($getSlot)) {
            $chkSlotUsage = PackageMemberDetail::where('slot_id', $getSlot[0]->id)->count();
            if ($chkSlotUsage>0) {
                $msg = "Package already in use";
                $response = ['message' => $msg];
                return (new HttpResource($response))
                    ->response($msg)
                    ->setStatusCode(400);
            }
            $packages = $getPackage[0];
            $packageBenefit = explode(',', $getPackage[0]->package_benefit);
            $packageDiscountPercentage = $getPackage[0]->package_discount_percentage;
            $packageDiscount = $getPackage[0]->package_discount;
            // echo "<pre>";
            // print_r($packageBenefit);
            // echo "</pre>";
            $yearCount = 1;
            $tmpYear = "";
            $yearPeriod = count($packageBenefit);
            $slotY = $getSlot[0]->slot_year;
            $slotYears = AvailableSlot::where('slot_week', '=', $slotWeek)->where('slot_year', '>=', $slotY)->select('*')->orderBy('slot_year', 'ASC')->limit($yearPeriod)->get();
            $slotYear = json_decode(json_encode($slotYears));
            // echo "<pre>";
            // print_r($slotYear);
            // echo "</pre>";

            $dateN = date('Y-m-d');
            // $dateNP = date('Y-m-d',strtotime($dateN . "+1 days"));
            $datePeriod = date_create($dateN);
            $sYearPeriod = date_format($datePeriod, 'Y-m-d 00:00:00');
            date_add($datePeriod, date_interval_create_from_date_string($yearPeriod.' years'));
            date_sub($datePeriod, date_interval_create_from_date_string('1 days'));
            $eYearPeriod = date_format($datePeriod, 'Y-m-d 23:59:59');
            // echo $sYearPeriod." : ".$eYearPeriod."<br />";
            $createCoupon = [];
            $slotMember = [];
            $stampTime = time();
            $time = time();
            $stampTime = date('His');
            foreach ($slotYear as $slotK => $slotV) {
                $packageStartDate = $slotV->slot_start_date;
                $dateNow = date('Y-m-d', strtotime($packageStartDate));

                $yearStep = $slotK+1;
                $yearSteps = ($yearStep==1 ? $yearStep.'st' : ($yearStep==2 ? $yearStep.'nd' : ($yearStep==3 ? $yearStep."rd" : $yearStep."th")));
                $dateY = date_create($dateNow);
                $sYearDate = date_format($dateY, 'Y-m-d 00:00:00');
                date_add($dateY, date_interval_create_from_date_string('1 years'));
                date_sub($dateY, date_interval_create_from_date_string('1 days'));
                $eYearDate = date_format($dateY, 'Y-m-d 23:59:59');
                $tmpYear = $eYearDate;
                // echo $sYearDate." : ".$eYearDate."<br />";
                $tmpDate = "";
                $benefitNight = $packageBenefit[$slotK];
                for ($c=0;$c<$benefitNight;$c++) {
                    $dateP = $dateNow;
                    $datePackageCode = date('YmdHis', strtotime($dateNow.$stampTime. "+".$c." days"));
                    if ($tmpDate != "") {
                        // $dateNow = $tmpDate;
                        $dateP = date('Y-m-d', strtotime($tmpDate . "+1 days"));
                    }
                    // echo $datePackageCode."<br />";
                    $dateB = date_create($dateP);
                    $sDate = date_format($dateB, 'Y-m-d 00:00:00');
                    // date_add($dateB, date_interval_create_from_date_string($c.' days'));
                    // date_sub($dateB, date_interval_create_from_date_string('1 days'));
                    $eDate = date_format($dateB, 'Y-m-d 23:59:59');
                    $tmpDate = $eDate;
                    // echo $sDate." : ".$eDate."<br />";
                    $cStep = $c+1;
                    $arrayCreate = [
                        'offer_title' => $getPackage[0]->package_name." ".$yearSteps." year"." (days ".$cStep.")",
                        'description' => $getPackage[0]->package_name." ".$benefitNight." night "." ".$yearSteps." year"." (days ".$cStep.") start ".$sDate."-".$eDate,
                        'image' => $getPackage[0]->package_image,
                        'period_start_time' => $sDate,
                        'period_end_time' => $eDate,
                        'code' => $getPackage[0]->package_code."_".$benefitNight."_".$yearSteps."y"."_d".$cStep."_".$this->_userId."_".$datePackageCode,
                        'type' => "PERCENTAGE",
                        'value' => 100,
                        'min_amount' => 0,
                        'max_amount' => 500000,
                        'limit_per_user' => 0,
                        'limit_per_coupon' => 0,
                        'status' => 1,
                    ];
                    $createCoupon[] = CouponMaster::create($arrayCreate);
                    // $createCoupon[] = $arrayCreate;
                    $slotMember[] = $slotV->id;
                }
            }
            // echo "<pre>";
            // print_r($createCoupon);
            // echo "</pre>";
            if (!empty($createCoupon)) {
                $arrayCreatePackageMember = [
                    'user_id' => $this->_userId,
                    'package_id' => $getPackage[0]->id,
                    'package_start_time' => $sYearPeriod,
                    'package_end_time' => $eYearPeriod,
                    'package_member_status' => 0,
                ];
                $createPackageMember = PackageMember::create($arrayCreatePackageMember);
                // $createPackageMember = $arrayCreatePackageMember;
                // echo "<pre>";
                // print_r($createPackageMember);
                // echo "</pre>";
                $packageMemberId = $createPackageMember->id;
                // $packageMemberId = $getPackage[0]->id;
                foreach ($createCoupon as $cK => $coupons) {
                    $arrayPackageMemberDetail = [
                        'package_member_id' => $packageMemberId,
                        'coupon_id' => $coupons->id,
                        'slot_id' => $slotMember[$cK],
                        'package_coupon' => 'FREENIGHT',
                        'status' => 1,
                    ];
                    $createPackageMemberDetail[] = PackageMemberDetail::create($arrayPackageMemberDetail);
                    // $createPackageMemberDetail[] = $arrayPackageMemberDetail;
                }
                // echo "<pre>";
                // print_r($createPackageMemberDetail);
                // echo "</pre>";
                // Create Coupon % off vip
                if ($packageDiscountPercentage > 0) {
                    $arrayCreateOff = [
                        'offer_title' => $getPackage[0]->package_name." (".$packageDiscountPercentage."% off)",
                        'description' => $getPackage[0]->package_name." ".$packageDiscountPercentage."% off start ".$sYearPeriod."-".$eYearPeriod,
                        'image' => $getPackage[0]->package_image,
                        'period_start_time' => $sYearPeriod,
                        'period_end_time' => $eYearPeriod,
                        'code' => $getPackage[0]->package_code."_".$packageDiscountPercentage."_".$this->_userId."_".$time,
                        'type' => "PERCENTAGE",
                        'value' => $packageDiscountPercentage,
                        'min_amount' => 0,
                        'max_amount' => 500000,
                        'limit_per_user' => 0,
                        'limit_per_coupon' => 0,
                        'status' => 1,
                    ];
                    $createCouponOff = CouponMaster::create($arrayCreateOff);

                    $arrayPackageMemberOff = [
                        'package_member_id' => $createPackageMember->id,
                        'coupon_id' => $createCouponOff->id,
                        'package_coupon' => 'ADDITIONAL',
                        'status' => 1,
                    ];
                    $createPackageMemberDetailOff = PackageMemberDetail::create($arrayPackageMemberOff);
                }
                if ($packageDiscount > 0) {
                    $arrayCreateDiscount = [
                        'offer_title' => $getPackage[0]->package_name." (".$packageDiscount.$getPackage[0]->package_currency.")",
                        'description' => $getPackage[0]->package_name." ".$packageDiscount.$getPackage[0]->package_currency." start ".$sYearPeriod."-".$eYearPeriod,
                        'image' => $getPackage[0]->package_image,
                        'period_start_time' => $sYearPeriod,
                        'period_end_time' => $eYearPeriod,
                        'code' => $getPackage[0]->package_code."_".$packageDiscount."_".$this->_userId."_".$time,
                        'type' => "FIXED",
                        'value' => $packageDiscount,
                        'min_amount' => 0,
                        'max_amount' => 500000,
                        'limit_per_user' => 0,
                        'limit_per_coupon' => 0,
                        'status' => 1,
                    ];

                    $createCouponDiscount = CouponMaster::create($arrayCreateDiscount);

                    $arrayPackageMemberDiscount = [
                        'package_member_id' => $createPackageMember->id,
                        'coupon_id' => $createCouponDiscount->id,
                        'package_coupon' => 'ADDITIONAL',
                        'status' => 1,
                    ];
                    $createPackageMemberDetailDiscount = PackageMemberDetail::create($arrayPackageMemberDiscount);
                }

                // Create Package Period By coupon id
                $memberPackage = "";
                if (!empty($createPackageMemberDetail)) {
                    foreach ($createPackageMemberDetail as $vC) {
                        $couponMembers[] = $vC->id;
                    }
                    $memberPackage = implode(',', $couponMembers);
                    if (!empty($createPackageMemberDetailOff)) {
                        $memberPackage .= ','.$createPackageMemberDetailOff->id;
                    }
                    if (!empty($createPackageMemberDetailDiscount)) {
                        $memberPackage .= ','.$createPackageMemberDetailDiscount->id;
                    }
                    //update Package member
                    $updatePackageMember = PackageMember::where('id', $packageMemberId)->update(['package_member' => $memberPackage]);
                }
            }
        } else {
            if (!empty($getUserPackage)) {
                $msg = "Package already";
                $response = ['message' => $msg];
                return (new HttpResource($response))
                    ->response($msg)
                    ->setStatusCode(400);
            }
        }

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource($response);
    }

    public function getPayment(Request $request)
    {
        $response = [];
        $arrayRequests = $request->all();
        if (!empty($arrayRequests)) {
            // Update VIP after payment success
            // $updateVIP = User::where('id', $userId)->update(['vip' => 1]);
        }
        return new HttpResource($response);
    }

    public function getMyPackage(Request $request)
    {
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        // $packageId = (isset($arrayRequests['packageId']))? $arrayRequests['packageId']:"";
        $dateNow = date('Y-m-d H:i:s');
        // $dateNP = date('Y-m-d H:i:s',strtotime($dateNow . "-1 days"));
        // echo $dateNow;
        $chkUserPackages = PackageMember::query()
        ->leftJoin('package_masters', 'package_members.package_id', '=', 'package_masters.id')
        ->where('package_members.user_id', $userId)
        ->where('package_members.package_start_time', '<=', $dateNow)
        ->where('package_members.package_end_time', '>=', $dateNow)
        ->where('package_member_status', '1')
        ->select(
            'package_members.id as package_member_id',
            'package_members.user_id',
            'package_members.package_id',
            'package_members.package_member',
            'package_members.package_start_time',
            'package_members.package_end_time',
            'package_members.package_member_status',
            'package_masters.id',
            'package_masters.package_code',
            'package_masters.package_name',
            'package_masters.package_type',
            'package_masters.package_benefit',
            'package_masters.package_discount_percentage',
            'package_masters.package_discount',
            'package_masters.package_price',
            'package_masters.package_currency',
            'package_masters.package_status',
            'package_masters.package_description',
            'package_masters.package_agreement',
            'package_masters.package_image'
        )
        ->get();
        $chkUserPackage = json_decode(json_encode($chkUserPackages));
        // echo "<pre>";
        // print_r($chkUserPackage);
        // echo "</pre>";

        if (!empty($chkUserPackage)) {
            foreach ($chkUserPackage as $uPackK => $uPackV) {
                $packageMember = explode(',', $uPackV->package_member);
                $getPackageMemberDetail = [];
                if (count($packageMember) > 0) {
                    foreach ($packageMember as $pMemberId) {
                        $getPackageMembers = PackageMemberDetail::query()
                        ->leftJoin('coupon_masters', 'package_member_details.coupon_id', '=', 'coupon_masters.id')
                        ->where('package_member_details.id', $pMemberId)->where('package_member_details.slot_id', '!=', '0')
                        // ->where('period_end_time', '>=', $dateNow)
                        ->get();
                        $pTmp = json_decode(json_encode($getPackageMembers));
                        if (!empty($pTmp)) {
                            $getPackageMember[] = $pTmp[0];
                        }
                    }
                }
                $getPackageMemberAdditionals = PackageMemberDetail::query()
                ->leftJoin('coupon_masters', 'package_member_details.coupon_id', '=', 'coupon_masters.id')
                ->where('package_member_details.package_member_id', $uPackV->package_member_id)
                ->where('package_member_details.slot_id', '0')
                ->where('package_member_details.package_coupon', 'ADDITIONAL')
                ->get();
                $getPackageMemberAdditional = json_decode(json_encode($getPackageMemberAdditionals));
                // echo "<pre>";
                // print_r($getPackageMember);
                // echo "</pre>";
                // echo "dddd";
                // echo "<pre>";
                // print_r($getPackageMemberAdditional);
                // echo "</pre>";
                $imgUrl = url('core/storage/app/images/Packages');
                $response = [
                    'appId' => $appId,
                    'userId' => $userId,
                    'packageId' => $uPackV->id,
                    'packageCode' => $uPackV->package_code,
                    'packageName' => $uPackV->package_name,
                    'packageType' => $uPackV->package_type,
                    'packageBenefit' => $uPackV->package_benefit,
                    'packagePrice' => $uPackV->package_price,
                    'packageCurrency' => $uPackV->package_currency,
                    'packageDetail' => $uPackV->package_description,
                    'packageAgreement' => $uPackV->package_agreement,
                    'packageImage' => [
                        'name' => $uPackV->package_code,
                        'description' => $uPackV->package_name,
                        'imgURL' => $imgUrl."/".$uPackV->package_image,
                    ],
                    'packageMember' => $getPackageMember,
                    'packageAdditionalMember' => $getPackageMemberAdditional[0],
                ];
            }
        }
        // $response = [
        //     'userId' => $userId,
        //     'appId' => $appId,
        //     'packageId' => '1',
        //     'packageName' => 'p1',
        //     'packageDescription' => 'package Description 1',
        //     'packagePrice' => 1,
        //     'packageCurrency' => 'USD',
        //     'packageStart' => '2020-02-01',
        //     'packageEnd' => '2025-02-01',
        //     'packageCode' => 'exclusive',
        //     'packageType' => 'vip',
        //     'packageImage' => [
        //         'name' => 'img1',
        //         'description' => 'image 1',
        //         'imgURL' => 'http://www.freeimageslive.com/galleries/nature/abstract/pics/snow_surface.jpg',
        //     ]
        // ];

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource($response);
    }

    public function getMyAvailablePackage(Request $request)
    {
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        // $packageId = (isset($arrayRequests['packageId']))? $arrayRequests['packageId']:"";
        $dateNow = date('Y-m-d H:i:s');
        // $dateNP = date('Y-m-d H:i:s',strtotime($dateNow . "-1 days"));
        // echo $dateNow;
        $chkUserPackages = PackagePeriod::query()
        ->leftJoin('package_masters', 'package_periods.package_id', '=', 'package_masters.id')
        ->where('user_id', $userId)
        ->where('package_period_start_time', '<=', $dateNow)
        ->where('package_period_end_time', '>=', $dateNow)
        ->where('status', '1')
        ->get();
        $chkUserPackage = json_decode(json_encode($chkUserPackages));
        // echo "<pre>";
        // print_r($chkUserPackage);
        // echo "</pre>";

        if (!empty($chkUserPackage)) {
            $packageCoupon = explode(',', $chkUserPackage[0]->package_coupon);
            $getPackagePeriod = [];
            if (count($packageCoupon) > 0) {
                foreach ($packageCoupon as $couponId) {
                    $chkUsagePackages = AppliedCouponCode::where('coupon_id', $couponId)
                    ->where('user_id', $userId)
                    ->get();
                    $chkUsagePackage = json_decode(json_encode($chkUsagePackages));
                    if (empty($chkUsagePackage)) {
                        $getPackagePeriods = CouponMaster::where('id', $couponId)
                        ->where('period_end_time', '>=', $dateNow)
                        ->where('period_start_time', '<=', $dateNow)
                        ->get();
                        $pTmp = json_decode(json_encode($getPackagePeriods));
                        if (!empty($pTmp)) {
                            $getPackagePeriod = $pTmp[0]->offer_title;
                        }
                    }
                }
            }
            $getPackagePeriodAdditionals = CouponMaster::where('id', $chkUserPackage[0]->package_additional_coupon)->get();
            $getPackagePeriodAdditional = json_decode(json_encode($getPackagePeriodAdditionals));
            switch ($getPackagePeriodAdditional[0]->type) {
                case 'PERCENTAGE':
                    $txtUnit = "%";
                break;
                default:
                    $txtUnit = "";
            }
            // echo "<pre>";
            // print_r($getPackagePeriod);
            // echo "</pre>";
            // echo "<pre>";
            // print_r($getPackagePeriodAdditional);
            // echo "</pre>";
            $imgUrl = url('core/storage/app/images/Packages');
            $response = [
                'appId' => $appId,
                'userId' => $userId,
                'packageId' => $chkUserPackage[0]->id,
                'packageCode' => $chkUserPackage[0]->package_code,
                'packageName' => $chkUserPackage[0]->package_name,
                'packageCoupon' => $getPackagePeriod,
                'packageAdditionalCoupon' => number_format($getPackagePeriodAdditional[0]->value)." ".$txtUnit,
            ];
        }

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource($response);
    }

    public function getMyAvailablePackageDetail(Request $request)
    {
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        // $packageId = (isset($arrayRequests['packageId']))? $arrayRequests['packageId']:"";
        $dateNow = date('Y-m-d H:i:s');
        // $dateNP = date('Y-m-d H:i:s',strtotime($dateNow . "-1 days"));
        // echo $dateNow;
        $chkUserPackages = PackagePeriod::query()
        ->leftJoin('package_masters', 'package_periods.package_id', '=', 'package_masters.id')
        ->where('user_id', $userId)
        ->where('package_period_start_time', '<=', $dateNow)
        ->where('package_period_end_time', '>=', $dateNow)
        ->where('status', '1')
        ->get();
        $chkUserPackage = json_decode(json_encode($chkUserPackages));
        // echo "<pre>";
        // print_r($chkUserPackage);
        // echo "</pre>";

        if (!empty($chkUserPackage)) {
            $packageCoupon = explode(',', $chkUserPackage[0]->package_coupon);
            $getPackagePeriod = [];
            if (count($packageCoupon) > 0) {
                foreach ($packageCoupon as $couponId) {
                    $chkUsagePackages = AppliedCouponCode::where('coupon_id', $couponId)
                    ->where('user_id', $userId)
                    ->get();
                    $chkUsagePackage = json_decode(json_encode($chkUsagePackages));
                    if (empty($chkUsagePackage)) {
                        $getPackagePeriods = CouponMaster::where('id', $couponId)
                        ->where('period_end_time', '>=', $dateNow)
                        ->get();
                        $pTmp = json_decode(json_encode($getPackagePeriods));
                        if (!empty($pTmp)) {
                            $getPackagePeriod[] = $pTmp[0];
                        }
                    }
                }
            }
            $getPackagePeriodAdditionals = CouponMaster::where('id', $chkUserPackage[0]->package_additional_coupon)->get();
            $getPackagePeriodAdditional = json_decode(json_encode($getPackagePeriodAdditionals));
            // echo "<pre>";
            // print_r($getPackagePeriod);
            // echo "</pre>";
            // echo "<pre>";
            // print_r($getPackagePeriodAdditional);
            // echo "</pre>";
            $imgUrl = url('core/storage/app/images/Packages');
            $response = [
                'appId' => $appId,
                'userId' => $userId,
                'packageId' => $chkUserPackage[0]->id,
                'packageCode' => $chkUserPackage[0]->package_code,
                'packageName' => $chkUserPackage[0]->package_name,
                'packageType' => $chkUserPackage[0]->package_type,
                'packageBenefit' => $chkUserPackage[0]->package_benefit,
                'packagePrice' => $chkUserPackage[0]->package_price,
                'packageCurrency' => $chkUserPackage[0]->package_currency,
                'packageDetail' => $chkUserPackage[0]->package_description,
                'packageAgreement' => $chkUserPackage[0]->package_agreement,
                'packageImage' => [
                    'name' => $chkUserPackage[0]->package_code,
                    'description' => $chkUserPackage[0]->package_name,
                    'imgURL' => $imgUrl."/".$chkUserPackage[0]->package_image,
                ],
                'packageCoupon' => $getPackagePeriod,
                'packageAdditionalCoupon' => $getPackagePeriodAdditional[0],
            ];
        }

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource($response);
    }

    public function getPromotion(Request $request)
    {
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";

        $getPromotions = BlogPost::where('cat_id', $this->posts_cat_id)->latest()->get();
        // echo "<pre>";
        // print_r($getPromotions);
        // echo "</pre>";
        foreach ($getPromotions as $pK => $promotions) {
            $response[] = [
                'promotionId' => $promotions['id'],
                'promotionName' => $promotions['title'],
                'promotionDetail' => $promotions['details'],
                'promotionAgreements' => $promotions['agreements'],
                'promotionThumbnail' => [
                    'name' => $promotions['title'],
                    'description' => $promotions['title'],
                    'imgURL' => asset('assets/backend/image/blog/post/'.$promotions['thumb']),
                ]
            ];
        }

        // $response = [
        //     [
        //         'promotionId' => '1',
        //         'promotionName' => 'p1',
        //         'promotionDetail' => 'promotion detail 1',
        //         'promotionImage' => [
        //             'name' => 'img1',
        //             'description' => 'image 1',
        //             'imgURL' => 'http://www.freeimageslive.com/galleries/nature/abstract/pics/snow_surface.jpg',
        //         ]
        //     ],
        //     [
        //         'promotionId' => '2',
        //         'promotionName' => 'p2',
        //         'promotionDetail' => 'promotion detail 2',
        //         'promotionImage' => [
        //             'name' => 'img2',
        //             'description' => 'image 2',
        //             'imgURL' => 'http://www.freeimageslive.com/galleries/nature/abstract/pics/winter_grass.jpg',
        //         ]
        //     ],
        // ];

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource($response);
    }

    public function getPromotionDetail(Request $request)
    {
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $promotionsId = (isset($arrayRequests['promotionsId']))? $arrayRequests['promotionsId']:"";

        $getPromotions = BlogPost::where('id', $promotionsId)->get();
        // echo "<pre>";
        // print_r($getPromotions);
        // echo "</pre>";
        $promotions = json_decode(json_encode($getPromotions));
        $response[] = [
            'promotionId' => $promotions[0]->id,
            'promotionName' => $promotions[0]->title,
            'promotionDetail' => $promotions[0]->details,
            'promotionAgreements' => $promotions[0]->agreements,
            'promotionThumbnail' => [
                'name' => $promotions[0]->title,
                'description' => $promotions[0]->title,
                'imgURL' => asset('assets/backend/image/blog/post/'.$promotions[0]->thumb),
            ],
            'promotionImage' => [
                'name' => $promotions[0]->title,
                'description' => $promotions[0]->title,
                'imgURL' => asset('assets/backend/image/blog/post/'.$promotions[0]->image),
            ]
        ];

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource($response);
    }

    public function someapp()
    {
        $getPackages = PackageMaster::where('package_status', 1)->get();
        $getPackage = json_decode(json_encode($getPackages));
        // echo "<pre>";
        // print_r($getPackage);
        // echo "</pre>";

        if (!empty($getPackage)) {
            $imgUrl = url('core/storage/app/images/Packages');
            foreach ($getPackage as $pK => $pV) {
                $response[] = [
                  'appId' => "test",
                  'userId' => "1",
                  'packageId' => $pV->id,
                  'packageCode' => $pV->package_code,
                  'packageName' => $pV->package_name,
                  'packageType' => $pV->package_type,
                  'packageBenefit' => $pV->package_benefit,
                  'packageDiscountPercentage' => $pV->package_discount_percentage,
                  'packageDiscount' => $pV->package_discount,
                  'packagePrice' => $pV->package_price,
                  'packageCurrency' => $pV->package_currency,
                  'packageDetail' => $pV->package_description,
                  // 'packageAgreement' => $pV->package_agreement,
                  'packageImage' => [
                      'name' => $pV->package_code,
                      'description' => $pV->package_name,
                      'imgURL' => $imgUrl."/".$pV->package_image,
                  ]
              ];
            }
        }

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        // return new HttpResource($response);
        return $response;
    }
}
