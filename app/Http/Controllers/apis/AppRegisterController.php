<?php

namespace App\Http\Controllers\apis;

use App\Http\Helper\MimeCheckRules;
use App\Model\CodeManager;
use App\Model\GeneralSetting;
use App\Model\User;
use App\Model\AppUser;
use App\Model\AppToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\HttpResource;
use App\Http\Resources\HeaderResource;

use Log;

// == Log Notify Note ==
// Log::emergency($error);
// Log::alert($error);
// Log::critical($error);
// Log::error($error);
// Log::warning($error);
// Log::notice($error);
// Log::info($error);
// Log::debug($error);

use Image;

class AppRegisterController extends Controller
{
    // private $user;

    // public function __construct(User $user)
    // {
    //     $this->user = $user;
    // }

    public function index()
    {
        return '200';
    }

    public function getAppID()
    {
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        // $charLength = 24;
        // $response = [];//Generate a random string.
        // $appId = openssl_random_pseudo_bytes($charLength);
        // //Convert the binary data into hexadecimal representation.
        // $appIds = bin2hex($appId);
        // //Print it out for example purposes.
        // // echo $token;
        // $arrayCreate = ['appId' => $appIds];
        $appId = $this->createAppID();
        $response = [
            'appId' => $appId['app_id']
        ];
        // $response = AppToken::first();
        // echo "<pre>";
        // print_r($response);
        // echo "</pre>";

        // return (new HttpResource ($arrayCreate))
        //     ->response()
        //     ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return new HttpResource($response);
    }

    public function createAppID()
    {
        $charLength = 24;
        $response = [];//Generate a random string.
        $appId = openssl_random_pseudo_bytes($charLength);
        //Convert the binary data into hexadecimal representation.
        $appIds = bin2hex($appId);
        //Print it out for example purposes.
        // echo $token;
        $arrayCreate = ['app_id' => $appIds];
        $response = AppToken::create($arrayCreate);
        return $response;
    }

    public function loginMember(Request $request)
    {
        // error_log($request);
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $msg = "";
        $arrayRequests = $request->all();

        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $username = (isset($arrayRequests['username']))? $arrayRequests['username']:"";
        $email = (isset($arrayRequests['email']))? $arrayRequests['email']:"";
        $password = (isset($arrayRequests['password']))? $arrayRequests['password']:"";
        $passwordEncrypt = password_hash($password, PASSWORD_BCRYPT);
        // $password_encrypt = Hash::make($password);
        // if($appId == ""){
        //     $aId = $this->getAppID();
        //     $get_appId = json_decode(json_encode($aId), true);
        //     $appId = $get_appId['appId'];
        // }

        // $arrayRequests['username'] = $username;
        // $arrayRequests['email'] = $email;
        // $arrayRequests['password'] = $password;
        // $arrayRequests['password_encrypt'] = $password_encrypt;
        // echo "<pre>";
        // print_r($arrayRequests);
        // echo "</pre>";
        // echo $password_encrypt;
        // echo "<br />";
        
        $getLogins = User::where(function ($q) use ($username, $email, $passwordEncrypt) {
            if ($username != "") {
                return $q->where('username', $username);
            } elseif ($email != "") {
                return $q->where('email', $email);
            }
        })->get();

        // error_log($getLogins);

        $getLogin = json_decode(json_encode($getLogins));
        // echo "<pre>";
        // print_r($getLogin);
        // echo "</pre>";
        if (!empty($getLogin)) {
            // (password_verify('rasmuslerdorf', $hash))
            // echo $getLogin[0]->password." : ".$password_encrypt;
            // echo "<br />";
            if (password_verify($password, $getLogin[0]->password)) {
                $msg = "Login Success";
                $users = new User();
                $users->sex = $getLogin[0]->sex;
                $gender = $users->sex();
                $imgUrl = url('core/storage/app/images/passportID/thumbnail');
                $response = [
                    'appId' => $appId,
                    'userId' => $getLogin[0]->id,
                    'username' => $getLogin[0]->username,
                    'email' => $getLogin[0]->email,
                    'fullName' => $getLogin[0]->full_name,
                    'phone' => $getLogin[0]->phone,
                    'sex' => $gender,
                    'picture' => $imgUrl.'/'.$getLogin[0]->picture,
                    'message' => $msg
                ];
            } else {
                $msg = "Username or Password is incorrect";
                $response = ['message' => $msg];
                return (new HttpResource($response))
                    ->response($msg)
                    ->setStatusCode(400);
            }
        } else {
            if ($username == "" && $email == "") {
                $msg = "Username or Email should not be empty";
            } else {
                $msg = "Username or Password is incorrect";
            }
            $response = ['message' => $msg];
            return (new HttpResource($response))
                ->response($msg)
                ->setStatusCode(400);
        }

        // $response = [
        //     'userId' => '1',
        //     'appId' => $appId,
        // ];

        // return (new HttpResource ($response))
        //     ->response()
        //     // ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return (new HttpResource($response))
                    ->response($msg)
                    ->setStatusCode(200);
    }


    // Register Member to the System
    public function registerMember(Request $request)
    {
        // Log::info($request);
        // error_log($request->get('username'));
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $msg = "";
        $arrayRequests = $request->all();

        // == Original ==
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $username = (isset($arrayRequests['username']))? $arrayRequests['username']:"";
        $email = (isset($arrayRequests['email']))? $arrayRequests['email']:"";
        $phone = (isset($arrayRequests['phone']))? $arrayRequests['phone']:"";
        $password = (isset($arrayRequests['password']))? $arrayRequests['password']:"";
        $confirmPassword = (isset($arrayRequests['confirmPassword']))? $arrayRequests['confirmPassword']:"";
        $passportId = (isset($arrayRequests['passportId']))? $arrayRequests['passportId']:"";
        $firstName = (isset($arrayRequests['firstName']))? $arrayRequests['firstName']:"";
        $lastName = (isset($arrayRequests['lastName']))? $arrayRequests['lastName']:"";
        $sex = (isset($arrayRequests['sex']))? $arrayRequests['sex']:"";
        $picture = (isset($arrayRequests['picture']))? $arrayRequests['picture']:"";

        // echo "<pre>";
        // print_r($picture);
        // echo "</pre>";

        $chkUs = User::where('username', $username)->get();
        $chkU = json_decode(json_encode($chkUs));
        if (!empty($chkU)) {
            $msg = 'Username Exists';
            $response = ['message' => $msg];
            return (new HttpResource($response))
                ->response($msg)
                ->setStatusCode(400);
        }

        $chkEs = User::where('email', $email)->get();
        $chkE = json_decode(json_encode($chkEs));
        if (!empty($chkE)) {
            $msg = 'Email Exists';
            $response = ['message' => $msg];
            return (new HttpResource($response))
                ->response($msg)
                ->setStatusCode(400);
        }


        $chkPass = ($password == $confirmPassword)?true:false;
        if (!$chkPass) {
            $msg = 'Password not match';
            $response = ['message' => $msg];
            return (new HttpResource($response))
                ->response($msg)
                ->setStatusCode(400);
        } else {
            // $arrayRequests['password'] = Hash::make($password);
            $arrayRequests['password'] = password_hash($password, PASSWORD_BCRYPT);
        }


        if (!empty($picture)) {
            $img = time().'.'.$picture->getClientOriginalExtension();
            error_log($img);
            $image = Image::make($picture->getRealPath());
            $thumbnailPath = storage_path('app/images/passportID/thumbnail');
            $image->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($thumbnailPath.'/'.$img);
            // $destinationPath = public_path('images/passportID');
            $destinationPath = storage_path('app/images/passportID/');
            $picture->move($destinationPath, $img);

            $arrayRequests['picture'] = $img;
        }


        // foreach ($arrayRequests as $key => $value) {
        //   // code...
        //   error_log(($key."::".$value));
        // }

        // Test add user to the System
        // $verified = array(
        //   "appId" => "78huh7hgbybhyujuhh8uh8iuh8i",
        // 	"username" => "tester02",
        // 	"email" => "tester02@gmail.com",
        // 	"password" => "tester1990",
        // 	"passport_id" => "1234567890123",
        // 	"first_name" => "test02",
        // 	"last_name" => "tester02",
        // 	"sex" => "M",
        // 	"picture" => ""
        // );

        $createUser = User::create($arrayRequests);
        // echo "<pre>";
        // print_r($createUser);
        // echo "</pre>"
        if ($createUser) {
            $msg = 'Register Success';
            $users = new User();
            $users->sex = $createUser->sex;
            $gender = $users->sex();
            $imgUrl = url('core/storage/app/images/passportID/thumbnail');

            $response = [
                'appId' => $appId,
                'userId' => $createUser->id,
                'username' => $createUser->username, //username
                'email' => $createUser->email,
                'fullName' => $createUser->full_name,
                'phone' => $createUser->phone,
                'sex' => $gender,
                'picture' => $imgUrl.'/'.$createUser->picture,
                'message' => $msg
            ];
        }
        // $registerMember = User::create($arrayRequests);

        // return (new HttpResource ($response))
        //     ->response()
        //     // ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return (new HttpResource($response))
                ->response($msg)
                ->setStatusCode(200);
    }


    public function resetPasswordMember(Request $request)
    {
        $headerResource = new HeaderResource();
        $headerBearer = $headerResource->getBearerToken();
        $response = [];
        $msg = "";
        $arrayRequests = $request->all();
        $appId = (isset($arrayRequests['appId']))? $arrayRequests['appId']:"";
        $username = (isset($arrayRequests['username']))? $arrayRequests['username']:"";
        $email = (isset($arrayRequests['email']))? $arrayRequests['email']:"";
        $oldPassword = (isset($arrayRequests['oldPassword']))? $arrayRequests['oldPassword']:"";
        $password = (isset($arrayRequests['password']))? $arrayRequests['password']:"";
        $confirmPassword = (isset($arrayRequests['confirmPassword']))? $arrayRequests['confirmPassword']:"";
        $passwordEncrypt = password_hash($password, PASSWORD_BCRYPT);
        // $arrayRequests['username'] = $username;
        // $arrayRequests['email'] = $email;
        // $arrayRequests['password'] = $password;
        // $arrayRequests['password_encrypt'] = $password_encrypt;
        // echo "<pre>";
        // print_r($arrayRequests);
        // echo "</pre>";
        // echo $appId;
        // echo "<br />";

        $getUsers = User::where(function ($q) use ($username, $email) {
            if ($username != "") {
                return $q->where('username', $username);
            } elseif ($email != "") {
                return $q->where('email', $email);
            }
        })->get();
        $getUser = json_decode(json_encode($getUsers));
        // echo "<pre>";
        // print_r($getUser);
        // echo "</pre>";
        if (!empty($getUser)) {
            if ($password == $confirmPassword) {
                if (password_verify($oldPassword, $getUser[0]->password)) {
                    $users = new User();
                    $users->sex = $getUser[0]->sex;
                    $gender = $users->sex();
                    $imgUrl = url('core/storage/app/images/passportID/thumbnail');
                    $resetPass = User::where(function ($q) use ($username, $email, $passwordEncrypt) {
                        if ($username != "") {
                            return $q->where('username', $username);
                        } elseif ($email != "") {
                            return $q->where('email', $email);
                        }
                    })->update(['password' => $passwordEncrypt]);
                    // echo "<pre>";
                    // print_r($resetPass);
                    // echo "</pre>";
                    $msg = "Reset Password Success";
                    if ($resetPass) {
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
                    }
                } else {
                    $msg = "Old password is incorrect";
                    $response = ['message' => $msg];
                    return (new HttpResource($response))
                        ->response($msg)
                        ->setStatusCode(400);
                }
            } else {
                $msg = "Password not match";
                $response = ['message' => $msg];
                return (new HttpResource($response))
                    ->response($msg)
                    ->setStatusCode(400);
            }
        } else {
            if ($username == "" && $email == "") {
                $msg = "Username or Email should not be empty";
            } else {
                $msg = "Username or Password is incorrect";
            }
            $response = ['message' => $msg];
            return (new HttpResource($response))
                ->response($msg)
                ->setStatusCode(400);
        }

        // return (new HttpResource ($response))
        //     ->response()
        //     // ->setStatusCode(200)
        //     ->header('X-token', $headerBearer);
        return (new HttpResource($response))
                ->response($msg)
                ->setStatusCode(200);
    }
}
