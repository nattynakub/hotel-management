<?php

namespace App\Http\Controllers\apis;

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

class AppNewsController extends Controller
{
    private $posts_cat_id = 2; //News

    public function index(){
        return '200';
    }
    
    public function getNews(){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $imgUrl = url('core/storage/app/images/Packages');
        
        $response = [
            [
                'newsId' => '1',
                'newsTitle' => 'n1',
                'newsDetail' => 'new detail 1',
                'newsUpdate' => '2019-08-01',
                'newsImage' => [
                    'name' => 'img1',
                    'description' => 'image 1',
                    'imgURL' => $imgUrl."/1566363783.jpg",
                ]
            ],
            [
                'newsId' => '2',
                'newsTitle' => 'n2',
                'newsDetail' => 'new detail 2',
                'newsUpdate' => '2019-08-01',
                'newsImage' => [
                    'name' => 'img2',
                    'description' => 'image 2',
                    'imgURL' => $imgUrl."/1566363783.jpg",
                ]
            ],
        ];

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }

    public function getNewsDetail($newsId){
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $imgUrl = url('core/storage/app/images/Packages');
        $response = [
            'newsId' => '1',
            'newsTitle' => 'n1',
            'newsDetail' => 'news detail 1',
            'newsUpdate' => '2019-08-01',
            'newsImage' => [
                'name' => 'img1',
                'description' => 'image 1',
                'imgURL' => $imgUrl."/1566363783.jpg",
            ]
        ];

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource ($response);
    }
}
