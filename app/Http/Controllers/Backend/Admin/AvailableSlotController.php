<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Model\User;
use App\Model\AvailableSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AvailableSlotController extends Controller
{
    /**
     * @var User
     */
    private $user;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $arrayRequests = $request->all();
        $slotName = (isset($arrayRequests['slotName']))? $arrayRequests['slotName']:"";
        $slotYear = (isset($arrayRequests['slotYear']))? $arrayRequests['slotYear']:date('Y');
        $slotStart = (isset($arrayRequests['slotStart']))? $arrayRequests['slotStart']:$slotYear.'-01-01';
        list($ySlot, $mSlot, $dSlot) = explode('-', $slotStart);
        if($ySlot != $slotYear){
            return 0;
        }
        $slotPeak = (isset($arrayRequests['slotPeak']))? $arrayRequests['slotPeak']:"NORMAL";
        $slotRoom = (isset($arrayRequests['slotRoom']))? $arrayRequests['slotRoom']:0;
        $slotRoomRemain = (isset($arrayRequests['slotRoomRemain']))? $arrayRequests['slotRoomRemain']:$slotRoom;
        $roomTypeId = (isset($arrayRequests['roomTypeId']))? $arrayRequests['roomTypeId']:0;

        $slotWeek = (isset($arrayRequests['slotWeek']))? $arrayRequests['slotWeek']:52;
        $dow = 7;
        $totalDate = $slotWeek*$dow;
        // echo $totalDate;
        // echo "<br />";
        $dateS = date_create($slotStart);
        $sDate = date_format($dateS, 'Y-m-d');
        date_add($dateS, date_interval_create_from_date_string($totalDate.' days'));
        $eDate = date_format($dateS, 'Y-m-d');

        $fDateStart = $sDate;
        $fDateEnd = $eDate;
        // echo $fDateStart.":".$fDateEnd;
        // echo "<br />";
        $count = 1;
        $arraySlot = [];
        $cSlot = 0;
        $slotKey = 0;
        $availableSlot = [];     
        $availableSlots = [];     
        $chkSlotYear = AvailableSlot::where('slot_year', $slotYear)->count();
        // echo $chkSlotYear;
        if($chkSlotYear == 0){
            while($cSlot < $slotWeek){
                for($i=0;$i<$dow;$i++){
                    if($i==0){
                        $startDate = $fDateStart;
                    }else if($i==($dow-1)){
                        $endDate = $fDateStart;
                    }
                    $fDateStart = date ("Y-m-d", strtotime("+1 days", strtotime($fDateStart)));
                }
                $cCode = $cSlot+1;
                $arraySlot[$cSlot]['slot_code'] = $slotCode = "FREENIGHT".$slotYear.str_pad($cCode, 2, '0', STR_PAD_LEFT);
                $arraySlot[$cSlot]['slot_name'] = $slotName = "FREENIGHT ".$slotYear." "."(Week ".$cCode.")";
                $arraySlot[$cSlot]['slot_year'] = $slotYear;
                $arraySlot[$cSlot]['slot_week'] = ($cSlot+1);
                $arraySlot[$cSlot]['slot_peak'] = $slotPeak;
                $arraySlot[$cSlot]['slot_start_date'] = $startDate;
                $arraySlot[$cSlot]['slot_end_date'] = $endDate;
                $arraySlot[$cSlot]['room_type_id'] = $roomTypeId;
                $arraySlot[$cSlot]['slot_room'] = $slotRoom;
                $arraySlot[$cSlot]['slot_room_remain'] = $slotRoom;
                $arraySlot[$cSlot]['slot_status'] = 0;
                
                $cSlot++;
            }
            // while (strtotime($fDateStart) < strtotime($fDateEnd)) {

            //     // echo $fDateStart." - ".$fDateEnd;
            //     // echo "<br />";
            //     $cCode = $cSlot+1;
            //     $arraySlot[$cSlot][$slotKey]['slotCode'] = $slotCode = "FREENIGHT".$slotYear.$cCode.$slotKey;
            //     $arraySlot[$cSlot][$slotKey]['slotName'] = $slotName = "FREENIGHT ".$slotYear." "."(Week ".$cCode.")";
            //     $arraySlot[$cSlot][$slotKey]['slotYear'] = $slotYear;
            //     $arraySlot[$cSlot][$slotKey]['startDate'] = $fDateStart." 00:00:00";
            //     $arraySlot[$cSlot][$slotKey]['endDate'] = $fDateStart." 23:59:59";
            //     $arraySlot[$cSlot][$slotKey]['roomType'] = $roomTypeId;
            //     $arraySlot[$cSlot][$slotKey]['slotRoom'] = $slotRoom;
            //     if($count % $dow == 0){
            //         $cSlot++;
            //         $tmpStart = $fDateStart;
            //         $slotKey = 0;
            //     }
            //     $fDateStart = date ("Y-m-d", strtotime("+1 days", strtotime($fDateStart)));
            //     $slotKey++;
            //     $count++;
            // }
            if(!empty($arraySlot)){
                foreach($arraySlot as $sKey => $sVal){
                    $availableSlot = AvailableSlot::create($sVal);
                    $availableSlots[] = json_decode(json_encode($availableSlot));
                }
                
            }
        }else{
            return 0;
        }

        echo $count;
        echo "<pre>";
        print_r($availableSlots);
        echo "</pre>";
        return $availableSlots;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AvailableSlot  $availableSlot
     * @return \Illuminate\Http\Response
     */
    public function show(AvailableSlot $availableSlot)
    {
        //
        $slotYear = $availableSlot->whereNull('deleted_at')->get();
        echo "<pre>";
        print_r($slotYear);
        echo "</pre>";
    }

    public function showSlotbyYear(Request $request, AvailableSlot $availableSlot)
    {
        //
        $arrayRequests = $request->all();
        $slotYear = (isset($arrayRequests['slotYear']))? $arrayRequests['slotYear']:date('Y');

        $slotYear = $availableSlot->where('slot_year', $slotYear)->whereNull('deleted_at')->get();
        echo "<pre>";
        print_r($slotYear);
        echo "</pre>";
    }

    public function showSlotYear(AvailableSlot $availableSlot)
    {
        //
        $slotYear = $availableSlot->select('slot_year')->groupBy('slot_year')->pluck('slot_year');
        echo "<pre>";
        print_r($slotYear);
        echo "</pre>";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AvailableSlot  $availableSlot
     * @return \Illuminate\Http\Response
     */
    public function edit(AvailableSlot $availableSlot)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AvailableSlot  $availableSlot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AvailableSlot $availableSlot)
    {
        //
    }

    public function updateSlotbyID(Request $request, AvailableSlot $availableSlot)
    {
        //
        $arrayRequests = $request->all();
        $slotYear = (isset($arrayRequests['slotYear']))? $arrayRequests['slotYear']:date('Y');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AvailableSlot  $availableSlot
     * @return \Illuminate\Http\Response
     */
    public function destroy(AvailableSlot $availableSlot)
    {
        //
    }

        /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AvailableSlot  $availableSlot
     * @return \Illuminate\Http\Response
     */
    public function updateSlotStatus($slotId, $slotStatus)
    {
        //
        // slot_status {0=deleted, 1=pending, 2=confirm}
        $response = [];
        $response = [
            'status' => false,
            'msg' => 'slot not found!',
        ];
        if($slotId != ""){
            $checkSlot = AvailableSlot::findOrFail($slotId);
            if($checkSlot){
                $res = $checkSlot->update(['slot_status' => $slotStatus]);
                if($res){
                    $response = [
                        'element' => $res,
                        'status' => true,
                        'msg' => 'update slot success',
                    ];
                }
            }
        }
        return $response;
    }
}
