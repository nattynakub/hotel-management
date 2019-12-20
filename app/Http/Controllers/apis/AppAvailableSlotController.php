<?php

namespace App\Http\Controllers\apis;

use App\Model\AvailableSlot;
use App\Model\PackageMemberDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\HttpResource;
use App\Http\Resources\HeaderResource;

class AppAvailableSlotController extends Controller
{

    public function getSlotbyYear(Request $request, AvailableSlot $availableSlot)
    {
        //
        $arrayRequests = $request->all();
        // echo "<pre>";
        // print_r($arrayRequests);
        // echo "</pre>";
        $slotYear = (isset($arrayRequests['slotYear']))? $arrayRequests['slotYear']:date('Y');

        $slotYear = $availableSlot->where('slot_year', $slotYear)->whereNull('deleted_at')->get();
        // echo "<pre>";
        // print_r($slotYear);
        // echo "</pre>";
        return new HttpResource ($slotYear);
    }

    public function getSlotYear(AvailableSlot $availableSlot)
    {
        //
        $slotYear = $availableSlot->select('slot_year')->groupBy('slot_year')->pluck('slot_year');
        // echo "<pre>";
        // print_r($slotYear);
        // echo "</pre>";
        return new HttpResource ($slotYear);
    }

    public function getAvailableSlotbyYear(Request $request, AvailableSlot $availableSlot)
    {
        $arrayRequests = $request->all();
        $tmpYear = intval($arrayRequests['slotYear']);
        $available = [];
        //
        $chkY = checkdate(1,1, $tmpYear);
        if($chkY && strlen($tmpYear) == 4){
            $startYear = $tmpYear;
        }else{
            $msg = "This year over deal";
            $response = ['message' => $msg];
            return (new HttpResource ($response))
                ->response($msg)
                ->setStatusCode(400);
        }

        $chkSlotUsage = PackageMemberDetail::where('status', '1')->where('slot_id', '!=', '0')->select('slot_id')->groupBy('slot_id')->pluck('slot_id');
        $chkSlot = '';
        // echo "<pre>";
        // print_r($chkSlotUsage);
        // echo "</pre>";

        $slotYears = $availableSlot->where('slot_year', '>=', $startYear)->select('slot_year')->groupBy('slot_year')->limit(5)->orderBy('slot_year', 'ASC')->get();
        $slotYear = json_decode(json_encode($slotYears));

        // $slotYear = $availableSlot->where('slot_year', '>=', $startYear)->whereNotIn('id', $chkSlotUsage)->select('*')->orderBy('slot_year', 'ASC')->get();
        // echo "<pre>";
        // print_r($slotYear);
        // echo "</pre>";
        if(count($slotYear) < 5){
            $msg = "This year over deal";
            $response = ['message' => $msg];
            return (new HttpResource ($response))
                ->response($msg)
                ->setStatusCode(400);
        }
        $tmpSlotY = 0;
        $week = 1;

        foreach($slotYear as $sK => $sV){
            // echo $sV->slot_year;
            $slotAYears = $availableSlot->where('slot_year', '=', $sV->slot_year)->where('slot_start_date', '>', date('Y-m-d H:i:s'))->whereNotIn('id', $chkSlotUsage)->select('*')->orderBy('slot_year', 'ASC')->get();
            $slotAYear = json_decode(json_encode($slotAYears));
            // echo "<pre>";
            // print_r($slotAYear);
            // echo "</pre>";
            $slotDetail = [];
            foreach($slotAYear as $yK => $yV){
                // $slotDetail[] = [
                //     'slotCode' => $sV['slot_code'],
                //     'slotName' => $sV['slot_name'],
                //     'slotStartDate' => $sV['slot_start_date'],
                //     'slotEndDate' => $sV['slot_end_date'],
                //     'slotYear' => $sV['slot_year'],
                //     'slotPeak' => $sV['slot_peak'],
                //     'slotWeek' => $sV['slot_week'],
                //     'roomTypeId' => $sV['room_type_id'],
                //     'slotStatus' => $sV['slot_status'],
                // ];
                $slotDetail[] = [
                    'slotCode' => $yV->slot_code,
                    'slotName' => $yV->slot_name,
                    'slotStartDate' => $yV->slot_start_date,
                    'slotEndDate' => $yV->slot_end_date,
                    'slotYear' => $yV->slot_year,
                    'slotPeak' => $yV->slot_peak,
                    'slotWeek' => $yV->slot_week,
                    'roomTypeId' => $yV->room_type_id,
                    'slotStatus' => $yV->slot_status,
                ];
                
            }
            if($tmpSlotY == $sV->slot_year){
                $available['Y'.$sV->slot_year] = $slotDetail;
            }else{
                $available['Y'.$sV->slot_year] = $slotDetail;
            }
            $tmpSlotY = $sV->slot_year;
            $week++;
        };
        // echo "<pre>";
        // print_r($available);
        // echo "</pre>";
        return new HttpResource ($available);
    }

}
