<?php

namespace App\Http\Controllers\apis;

use App\Http\Helper\MimeCheckRules;
use App\Model\CodeManager;
use App\Model\GeneralSetting;
use App\Model\User;
use App\Model\Reservation;
use App\Model\Floor;
use App\Model\Room;
use App\Model\RoomType;
use App\Model\PackageMaster;
use App\Model\PackagePeriod;
use App\Model\PackageMember;
use App\Model\PackageMemberDetail;
use App\Model\CouponMaster;
use App\Model\AvailableSlot;
use App\Model\AppliedCouponCode;
use App\Model\LogTransactions;
use App\Http\Controllers\Backend\Admin\AvailableSlotController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\HttpResource;
use App\Http\Resources\HeaderResource;

class AppReservationController extends Controller
{
    public $_userId;
    public $_appId;
    public $_tokenId;

    public function index(){
        return '200';
    }

    public function reservations(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";

        $checkinDate = (isset($arrayRequests['checkinDate']))? $arrayRequests['checkinDate']:date('Y-m-d');
        $durations = (isset($arrayRequests['duration']))? $arrayRequests['duration']:0;
        $totalGuest = (isset($arrayRequests['totalGuest']))? $arrayRequests['totalGuest']:0;
        $totalRoom = (isset($arrayRequests['totalRoom']))? $arrayRequests['totalRoom']:0;
        $floorId = (isset($arrayRequests['floorId']))? $arrayRequests['floorId']:0;
        $roomId = (isset($arrayRequests['roomId']))? $arrayRequests['roomId']:0;
        $paymentSelect = (isset($arrayRequests['paymentSelect']))? $arrayRequests['paymentSelect']:0;
        $checkoutDate = date('Y-m-d', strtotime($checkinDate." +".$durations." days"));
        $arrayRequests['checkoutDate'] = $checkoutDate;
        if(strtotime($checkinDate) <= time()){
            return $response = [
                'status' => false,
                'msg' => 'Check in date not available!',
            ];
        }
        $checkAvailables = PackageMember::query()
        ->where('package_start_time','<=', $checkinDate)
        ->where('package_end_time', '>=', $checkinDate)
        ->where('user_id', $this->_userId)
        ->where('package_member_status', '1')
        ->get();
        // $avaibleSlot = new AvailableSlotController();
        // $updateSlot = $avaibleSlot->updateSlotStatus('1', '2');
        if(count($checkAvailables)>0){
            // TO DO package member is exist
            // echo "<pre>";
            // print_r($checkAvailables[0]);
            // echo "</pre>";
            $checkAvailable = $checkAvailables[0];
            // echo $checkAvailable['package_member']."<br />";
            $packageMember = explode(",", $checkAvailable['package_member']);
            $startCheckinDate = $checkinDate;
            // echo "TO DO";
            // echo $startCheckinDate." <= ".$checkoutDate;
            // echo "<br />";
            $chkCoupon = [];
            while (strtotime($startCheckinDate) <= strtotime($checkoutDate)) {
                // echo $startCheckinDate;
                // echo "<br />";
                echo date("Y-m-d 00:00:00", strtotime($startCheckinDate));
                echo "<br />";
                $chkCoupon = PackageMemberDetail::query()
                ->leftJoin('coupon_masters as cm', 'cm.id', 'package_member_details.coupon_id')
                ->where('cm.period_start_time', date("Y-m-d 00:00:00", strtotime($startCheckinDate)))
                ->whereIN('package_member_details.id', $packageMember)
                ->get();
                if(count($chkCoupon) > 0){
                    // echo "<pre>";
                    // print_r($chkCoupon);
                    // echo "</pre>";
                    echo $chkCoupon[0]->package_coupon." : ".$chkCoupon[0]->description;
                    echo "<br />";
                }else{
                    echo "Over";
                    echo "<br />";
                    $checkSlotByDate = AvailableSlot::query()
                    ->where('slot_start_date','<=', $startCheckinDate)
                    ->where('slot_end_date', '>=', $startCheckinDate)
                    ->where('slot_status', '1')
                    ->count();
                    echo $checkSlotByDate;
                    echo "<br />";
                }
                    
                    
                $startCheckinDate = date ("Y-m-d", strtotime("+1 days", strtotime($startCheckinDate)));
                
            }

        }else{
            // TO DO package member is not exist

        }
        $response = [
            
        ];

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }

    public function bookingRoom(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $checkinDate = (isset($arrayRequests['checkinDate']))? $arrayRequests['checkinDate']:date('Y-m-d');
        $duration = (isset($arrayRequests['duration']))? $arrayRequests['duration']:0;
        $totalGuest = (isset($arrayRequests['totalGuest']))? $arrayRequests['totalGuest']:0;
        $totalRoom = (isset($arrayRequests['totalRoom']))? $arrayRequests['totalRoom']:0;
        $floorId = (isset($arrayRequests['floorId']))? $arrayRequests['floorId']:0;
        $roomId = (isset($arrayRequests['roomId']))? $arrayRequests['roomId']:0;
        $paymentSelect = (isset($arrayRequests['paymentSelect']))? $arrayRequests['paymentSelect']:0;
        $status = 1;
        $msg = "Success !!";

        $getReservation = Reservation::create();

        // echo "<pre>";
        // print_r($arrayRequests);
        // echo "</pre>";
        echo json_encode($arrayRequests);
        // $log = LogTransactions::create($arrayRequests);
        
        $response = [
            'userId' => $this->_userId,
            'appId' => $this->_appId,
            'status' => $status,
            'message' => $msg,

        ];

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }


    public function calculatePrice(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $checkinDate = (isset($arrayRequests['checkinDate']))? $arrayRequests['checkinDate']:date('Y-m-d');
        $durations = (isset($arrayRequests['duration']))? $arrayRequests['duration']:0;
        $checkoutDate = date('Y-m-d', strtotime($checkinDate." +".$durations));
        $arrayRequests['checkoutDate'] = $checkoutDate;
        $totalGuest = (isset($arrayRequests['totalGuest']))? $arrayRequests['totalGuest']:0;
        $totalKid = (isset($arrayRequests['totalKid']))? $arrayRequests['totalKid']:0;
        $totalRoom = (isset($arrayRequests['totalRoom']))? $arrayRequests['totalRoom']:0;
        $floorId = (isset($arrayRequests['floorId']))? $arrayRequests['floorId']:0;
        $roomId = (isset($arrayRequests['roomId']))? $arrayRequests['roomId']:0;
        $paymentSelect = (isset($arrayRequests['paymentSelect']))? $arrayRequests['paymentSelect']:0;
        $status = 1;
        $msg = "Success !!";

        echo "<pre>";
        print_r($arrayRequests);
        echo "</pre>";
        $getRooms = Room::query()
        ->leftJoin('room_types as rtype', 'rooms.room_type_id', 'rtype.id')
        ->where('rooms.id', $roomId)
        ->get();
        // echo json_encode($arrayRequests);
        $getRoom = json_decode(json_encode($getRooms));
        echo "<pre>";
        print_r($getRoom);
        echo "</pre>";

        $response = [
            'userId' => $this->_userId,
            'appId' => $this->_appId,
            'price' => 1,
            'discount' => 1,
            'tax' => 1,
            'priceTotal' => 1,
        ];

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }

   
    public function checkPackageAvailable(){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $this->_appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $this->_userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $checkinDate = (isset($arrayRequests['checkinDate']))? $arrayRequests['checkinDate']:date('Y-m-d');
        $durations = (isset($arrayRequests['duration']))? $arrayRequests['duration']:0;

        

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }


}
