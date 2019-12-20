<?php

namespace App\Http\Controllers\apis;

use App\Http\Helper\MimeCheckRules;
use App\Model\GeneralSetting;
use App\Model\Floor;
use App\Model\Room;
use App\Model\RoomType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\HttpResource;
use App\Http\Resources\HeaderResource;

class AppRoomController extends Controller
{
    public function index(){
        return '200';
    }
    
    public function getRoom(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";

        // $getRooms = Room::all();
        // $getFloors = Floor::first();
        // $getRoomTypes = RoomType::first();
                // echo "<pre>";
        // print_r($getRoom);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($getFloors);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($getRoomTypes);
        // echo "</pre>";
        $getRooms = Room::query()
        ->leftJoin('floors', 'rooms.floor_id', '=', 'floors.id')
        ->leftJoin('room_types as rtype', 'rooms.room_type_id', '=', 'rtype.id')
        // ->where('rooms.status', '1')
        ->whereNull('rooms.deleted_at')
        ->select('rooms.id', 'rooms.room_type_id', 'rooms.floor_id', 'rooms.image', 'rooms.number as room_number','rooms.status as room_status','floors.name as floor_name','floors.number as floor_number','floors.description as floor_description','floors.status as floor_status','rtype.title','rtype.slug','rtype.short_code','rtype.description as room_type_description','rtype.higher_capacity','rtype.kids_capacity','rtype.base_price','rtype.status as room_type_status')
        ->get();
        // $getRoom = json_decode(json_encode($getRooms));

        foreach($getRooms as $rooms){
            switch($rooms->room_status){
                case '1':
                    $roomStatus = 'avalable';
                break;
                case '0': default:
                    $roomStatus = 'unavalable';
                break;
            }
            $response[] = [
                'roomId' => $rooms->id,
                'roomFloor' => $rooms->floor_name,
                'roomNumber' => $rooms->room_number,
                'roomTypeId' => $rooms->room_type_id,
                'roomTypeCode' => $rooms->short_code,
                'roomTypeTitle' => $rooms->title,
                'roomHigherCapacity' => $rooms->higher_capacity,
                'roomKidsCapacity' => $rooms->kids_capacity,
                'roomPrice' => $rooms->base_price,
                // 'roomCurrency' => 'THB',
                'roomImage' => [
                    'name' => $rooms->room_number,
                    'description' => $rooms->floor_description,
                    // 'imgURL' => 'https://images.pexels.com/photos/164595/pexels-photo-164595.jpeg',
                    'imgUrl' => $rooms->image,
                ],
                'roomStatus' => $roomStatus, // {0 = Not Avai, 1 = Avai}
            ];
        }
        
        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }
    

    public function getRoomDetail(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $roomId = (isset($arrayRequests['roomId']))? $arrayRequests['roomId']:"";

        $getRooms = Room::query()
        ->leftJoin('floors', 'rooms.floor_id', '=', 'floors.id')
        ->leftJoin('room_types as rtype', 'rooms.room_type_id', '=', 'rtype.id')
        // ->where('rooms.status', '1')
        ->where('rooms.id', $roomId)
        ->whereNull('rooms.deleted_at')
        ->select('rooms.id', 'rooms.room_type_id', 'rooms.floor_id', 'rooms.image', 'rooms.number as room_number','rooms.status as room_status','floors.name as floor_name','floors.number as floor_number','floors.description as floor_description','floors.status as floor_status','rtype.title','rtype.slug','rtype.short_code','rtype.description as room_type_description','rtype.higher_capacity','rtype.kids_capacity','rtype.base_price','rtype.status as room_type_status')
        ->get();
        $getRoom = json_decode(json_encode($getRooms));
        if(!empty($getRoom)){
            switch($getRoom[0]->room_status){
                case '1':
                    $roomStatus = 'avalable';
                break;
                case '0': default:
                    $roomStatus = 'unavalable';
                break;
            }
            $response = [
                'roomId' => $getRoom[0]->id,
                'roomFloorId' => $getRoom[0]->floor_id,
                'roomFloor' => $getRoom[0]->floor_name,
                'roomNumber' => $getRoom[0]->room_number,
                'roomTypeId' => $getRoom[0]->room_type_id,
                'roomTypeCode' => $getRoom[0]->short_code,
                'roomTypeTitle' => $getRoom[0]->title,
                'roomHigherCapacity' => $getRoom[0]->higher_capacity,
                'roomKidsCapacity' => $getRoom[0]->kids_capacity,
                'roomPrice' => $getRoom[0]->base_price,
                // 'roomCurrency' => 'THB',
                'roomImage' => [
                    'name' => $getRoom[0]->room_number,
                    'description' => $getRoom[0]->title,
                    // 'imgURL' => 'https://images.pexels.com/photos/164595/pexels-photo-164595.jpeg',
                    'imgUrl' => $getRoom[0]->image,
                ],
                'roomStatus' => $roomStatus, // {0 = Not Avai, 1 = Avai}
            ];
        }
        

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }

    public function getAvailableRoom(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $floorId = (isset($arrayRequests['floorId']))? $arrayRequests['floorId']:"";

        $getRooms = Room::query()
        ->leftJoin('floors', 'rooms.floor_id', '=', 'floors.id')
        ->leftJoin('room_types as rtype', 'rooms.room_type_id', '=', 'rtype.id')
        // ->where('rooms.status', '1')
        ->where('rooms.id', $roomId)
        ->where('rooms.floor_id', $floorId)
        ->whereNull('rooms.deleted_at')
        ->select('rooms.id', 'rooms.room_type_id', 'rooms.floor_id', 'rooms.image', 'rooms.number as room_number','rooms.status as room_status','floors.name as floor_name','floors.number as floor_number','floors.description as floor_description','floors.status as floor_status','rtype.title','rtype.slug','rtype.short_code','rtype.description as room_type_description','rtype.higher_capacity','rtype.kids_capacity','rtype.base_price','rtype.status as room_type_status')
        ->get();

        // echo "<pre>";
        // print_r($getRooms);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($getFloors);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($getRoomTypes);
        // echo "</pre>";

        foreach($getRooms as $rK => $rooms){
            // $response[] = [
            //     'roomId' => $rV['id'],
            //     'roomFloor' => $rV['floor_id'],
            //     'roomNumber' => $rV['number'],
            //     'roomTypeId' => $rV['room_type_id'],
            //     'roomImage' => [
            //         'name' => $rV['number'],
            //         'description' => $rV['number'],
            //         'imgURL' => 'https://images.pexels.com/photos/164595/pexels-photo-164595.jpeg',
            //     ],
            //     'roomStatus' => $rV['status'], // {0 = Not Avai, 1 = Avai}
                
            // ]; 
            switch($rooms[0]->room_status){
                case '1':
                    $roomStatus = 'avalable';
                break;
                case '0': default:
                    $roomStatus = 'unavalable';
                break;
            }
            $response[] = [
                'roomId' => $rooms->id,
                'roomFloor' => $rooms->floor_name,
                'roomNumber' => $rooms->room_number,
                'roomTypeId' => $rooms->room_type_id,
                'roomTypeCode' => $rooms->short_code,
                'roomTypeTitle' => $rooms->title,
                'roomHigherCapacity' => $rooms->higher_capacity,
                'roomKidsCapacity' => $rooms->kids_capacity,
                'roomPrice' => $rooms->base_price,
                // 'roomCurrency' => 'THB',
                'roomImage' => [
                    'name' => $rooms->room_number,
                    'description' => $rooms->floor_description,
                    // 'imgURL' => 'https://images.pexels.com/photos/164595/pexels-photo-164595.jpeg',
                    'imgUrl' => $rooms->image,
                ],
                'roomStatus' => $roomStatus, // {0 = Not Avai, 1 = Avai}
            ];

        }
      
        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }

    public function getAvailableRoombyFloor(Request $request){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $userId = (isset($arrayRequests['userId']))? $arrayRequests['userId']:"";
        $floorId = (isset($arrayRequests['floorId']))? $arrayRequests['floorId']:"";

        $getRooms = Room::query()
        ->leftJoin('floors', 'rooms.floor_id', '=', 'floors.id')
        ->leftJoin('room_types as rtype', 'rooms.room_type_id', '=', 'rtype.id')
        // ->where('rooms.status', '1')
        ->where('rooms.floor_id', $floorId)
        ->whereNull('rooms.deleted_at')
        ->select('rooms.id', 'rooms.room_type_id', 'rooms.floor_id', 'rooms.image', 'rooms.number as room_number','rooms.status as room_status','floors.name as floor_name','floors.number as floor_number','floors.description as floor_description','floors.status as floor_status','rtype.title','rtype.slug','rtype.short_code','rtype.description as room_type_description','rtype.higher_capacity','rtype.kids_capacity','rtype.base_price','rtype.status as room_type_status')
        ->get();

        // echo "<pre>";
        // print_r($getRooms);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($getFloors);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($getRoomTypes);
        // echo "</pre>";

        foreach($getRooms as $rK => $rooms){
            switch($rooms->room_status){
                case '1':
                    $roomStatus = 'avalable';
                break;
                case '0': default:
                    $roomStatus = 'unavalable';
                break;
            }
            $response[] = [
                'roomId' => $rooms->id,
                'roomFloor' => $rooms->floor_name,
                'roomNumber' => $rooms->room_number,
                'roomTypeId' => $rooms->room_type_id,
                'roomTypeCode' => $rooms->short_code,
                'roomTypeTitle' => $rooms->title,
                'roomHigherCapacity' => $rooms->higher_capacity,
                'roomKidsCapacity' => $rooms->kids_capacity,
                'roomPrice' => $rooms->base_price,
                // 'roomCurrency' => 'THB',
                'roomImage' => [
                    'name' => $rooms->room_number,
                    'description' => $rooms->floor_description,
                    // 'imgURL' => 'https://images.pexels.com/photos/164595/pexels-photo-164595.jpeg',
                    'imgUrl' => $rooms->image,
                ],
                'roomStatus' => $roomStatus, // {0 = Not Avai, 1 = Avai}
            ];
        }
      
        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }
    
    public function getFloor(){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];

        $getFloors = Floor::get();
    
        foreach($getFloors as $fK => $fV){
            $response[] = [
                "floorId" => $fV['id'],
                "floorName" => $fV['name'],
                "floorNumber" => $fV['number'],
                "floorDescription" => $fV['description'],
                "floorStatus" => $fV['status'],
            ]; 
        }
        
        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }

    public function getRoomType(){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];

        $getRoomTypes = RoomType::get();
    
        foreach($getRoomTypes as $rtK => $rtV){
            $response[] = [
                "roomTypeId" => $rtV['id'],
                "roomTypeTitle" => $rtV['title'],
                "roomTypeSlug" => $rtV['slug'],
                "roomTypeShortCode" => $rtV['short_code'],
                "roomTypeDescription" => $rtV['description'],
                "roomTypeShortDescription" => $rtV['short_description'],
                "roomTypeHigherCapacity" => $rtV['higher_capacity'],
                "roomTypeKidsCapacity" => $rtV['kids_capacity'],
                "roomTypeBasePrice" => $rtV['base_price'],
                "roomTypeStatus" => $rtV['status'],
            ]; 
        }
        
        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }
}
