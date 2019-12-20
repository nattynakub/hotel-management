<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('test', 'apis\TestController@index')->name('test');
Route::get('testResponse', 'apis\TestController@testResponse')->name('testResponse');

// Member Management
Route::get('getAppID', 'apis\AppRegisterController@getAppID')->name('getAppID');
Route::post('loginMember', 'apis\AppRegisterController@loginMember')->name('loginMember');
Route::post('registerMember', 'apis\AppRegisterController@registerMember')->name('registerMember');
Route::post('resetPasswordMember', 'apis\AppRegisterController@resetPasswordMember')->name('resetPasswordMember');

// Profile
Route::post('getProfiles', 'apis\AppProfileController@getProfile')->name('getProfile');
Route::post('getHistorys', 'apis\AppProfileController@getHistory')->name('getHistory');

// Package Management
// Test API
Route::get('someapp', 'apis\AppPackageController@someapp')->name('someapp');

Route::post('registerPackages', 'apis\AppPackageController@registerPackage')->name('registerPackage');
Route::post('registerPackageNews', 'apis\AppPackageController@registerPackageNew')->name('registerPackageNew');
Route::post('addPackages', 'apis\AppPackageController@addPackage')->name('addPackage');
Route::post('getPackages', 'apis\AppPackageController@getPackage')->name('getPackage');
Route::post('storePackages', 'Backend\Admin\HotelConfigure\PackageMasterController@store')->name('storePackage');
Route::post('getPackageDetails', 'apis\AppPackageController@getPackageDetail')->name('getPackageDetail');
Route::post('getMyPackages', 'apis\AppPackageController@getMyPackage')->name('getMyPackage');
Route::post('getMyAvailablePackages', 'apis\AppPackageController@getMyAvailablePackage')->name('getMyAvailablePackage');
Route::post('getMyAvailablePackageDetails', 'apis\AppPackageController@getMyAvailablePackageDetail')->name('getMyAvailablePackageDetail');
Route::post('getPromotions', 'apis\AppPackageController@getPromotion')->name('getPromotion');
Route::post('getPromotionDetails', 'apis\AppPackageController@getPromotionDetail')->name('getPromotionDetail');
Route::post('getPromotionCodes', 'apis\AppPromotionController@getPromotionCode')->name('getPromotionCode');

// Room
Route::post('getRooms', 'apis\AppRoomController@getRoom')->name('getRoom');
Route::post('getRoomDetails', 'apis\AppRoomController@getRoomDetail')->name('getRoomDetail');

Route::post('getAvailableRooms', 'apis\AppRoomController@getAvailableRoom')->name('getAvailableRoom');
Route::post('getAvailableRoombyFloors', 'apis\AppRoomController@getAvailableRoombyFloor')->name('getAvailableRoombyFloor');
Route::get('getFloors', 'apis\AppRoomController@getFloor')->name('getFloor');
Route::get('getRoomTypes', 'apis\AppRoomController@getRoomType')->name('getRoomType');


// News
Route::get('getNews', 'apis\AppNewsController@getNews')->name('getNews');
Route::get('getNewsDetails/{newsId}', 'apis\AppNewsController@getNewsDetail')->name('getNewsDetail');

// Point&Reward Management
Route::post('getPoints', 'apis\AppPointController@getPoint')->name('getPoint');
Route::post('getRewards', 'apis\AppRewardController@getReward')->name('getReward');
Route::post('getRewardDetails', 'apis\AppRewardController@getRewardDetail')->name('getRewardDetail');
Route::post('getMyRewards', 'apis\AppRewardController@getMyReward')->name('getMyReward');

Route::post('addPoints', 'apis\AppPointController@addPoint')->name('addPoint');
Route::post('redeemPoints', 'apis\AppPointController@redeemPoint')->name('redeemPoint');

Route::post('redemptionRewards', 'apis\AppRewardController@redemptionReward')->name('redemptionReward');

// Partner
Route::post('addPartners', 'apis\AppPartnerController@addPartner')->name('addPartner');
Route::post('editPartners', 'apis\AppPartnerController@editPartner')->name('editPartner');

//Payment
Route::post('requestPayments', 'apis\AppReservationController@index')->name('requestPayment');
Route::post('requestPaymentMethods', 'apis\AppReservationController@index')->name('requestPaymentMethod');

// Reservation
Route::post('bookingRooms', 'apis\AppReservationController@bookingRoom')->name('bookingRoom');
Route::post('calculatePrices', 'apis\AppReservationController@calculatePrice')->name('calculatePrice');
Route::post('reservations', 'apis\AppReservationController@reservations')->name('reservations');

// Assignment
Route::post('assignmentPackages', 'apis\AppAssignmentController@assignmentPackage')->name('assignmentPackage');

//Package Exchange
Route::post('postPackageMemberExchanges', 'apis\AppPackageExchangeController@postPackageMemberExchange')->name('postPackageMemberExchange');
// Route::post('requestPackageMemberExchanges', 'apis\AppPackageExchangeController@requestPackageMemberExchange')->name('requestPackageMemberExchange');
Route::post('requestPackageMemberExchanges', 'apis\AppPackageExchangeController@requestOnPostPackageMemberExchange')->name('requestOnPostPackageMemberExchange');
Route::post('approvePackageExchanges', 'apis\AppPackageExchangeController@approvePackageExchange')->name('approvePackageExchange');
Route::post('rejectPackageExchanges', 'apis\AppPackageExchangeController@rejectPackageExchange')->name('rejectPackageExchange');
Route::post('showPackageExchanges', 'apis\AppPackageExchangeController@showPackageExchange')->name('showPackageExchange');
Route::post('historyPackageExchanges', 'apis\AppPackageExchangeController@historyPackageExchange')->name('historyPackageExchange');
Route::post('packageExchangesDetails', 'apis\AppPackageExchangeController@packageExchangesDetail')->name('packageExchangesDetail');
Route::post('availablePackageExchanges', 'apis\AppPackageExchangeController@availablePackageExchange')->name('availablePackageExchange');

// Admin slot
Route::post('storeSlots', 'Backend\Admin\AvailableSlotController@store')->name('storeSlot');
// Route::post('showSlotbyYears', 'Backend\Admin\AvailableSlotController@showSlotbyYear')->name('showSlotbyYear');
// Route::post('showSlotYears', 'Backend\Admin\AvailableSlotController@showSlotYear')->name('showSlotYear');

// App slot
Route::post('getSlotYears', 'apis\AppAvailableSlotController@getSlotYear')->name('getSlotYear');
Route::post('getSlotbyYears', 'apis\AppAvailableSlotController@getSlotbyYear')->name('getSlotbyYear');
Route::post('getAvailableSlotbyYears', 'apis\AppAvailableSlotController@getAvailableSlotbyYear')->name('getAvailableSlotbyYear');




// JUST FOR TEST //
Route::get(
  'testapi',
  "apis\TestApiController@test"
) -> name('test');
