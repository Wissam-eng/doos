<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarOwnerController;
use App\Http\Controllers\CarRenterController;
use App\Http\Controllers\DoosUsersController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\CarsController;
use App\Http\Controllers\CarOwnerPaymentInfoController;




Route::get('/test', function () {
    return ['message' => 'Hello from API'];
});

//------------------------------------reset_pass_verfiy_email------------------------------------------------------------------
Route::post('sendOTP', [AuthController::class, 'sendOTP'])->name('sendOTP');
Route::post('receiveOTP', [AuthController::class, 'receiveOTP'])->name('receiveOTP');
Route::post('resetpassword', [AuthController::class, 'resetpassword'])->name('resetpassword');
Route::post('verfiy_email', [AuthController::class, 'verfiy_email'])->name('verfiy_email');

//-----------------------------------------------------------------------------------------


Route::post('login', [AuthController::class, 'login'])->name('login');



//---------------------public routes-------------------------------------------------------


//---------create car owner
Route::post('add_car_owner', [CarOwnerController::class, 'store'])->name('add_car_owner');

//---------create car renter
Route::post('add_car_renter', [CarRenterController::class, 'store'])->name('add_car_renter');

//-------------------------doos users routes--------------------------------------------------------------

Route::middleware('auth:doos_users', 'role:owner')->group(function () {

    Route::post('create_user', [DoosUsersController::class, 'store'])->name('create_user');


    Route::get('get_car_owner', [CarOwnerController::class, 'index'])->name('get_car_owner');

    // resources membership
    Route::resource('membership', MembershipController::class);
});


Route::middleware('auth:doos_users', 'role:manager')->group(function () {});


Route::middleware('auth:doos_users', 'role:support')->group(function () {});

//------------------------------------car owner------------------------------------------------------------------
Route::middleware('auth:car_owners')->group(function () {

    Route::post('update_owner/{id}', [CarOwnerController::class, 'update'])->name('update_owner');
    Route::delete('delete_car_owner/{id}', [CarOwnerController::class, 'destroy'])->name('delete_car_owner');


    //---------------------------car owner payment info-------------------------------------------------------------
    Route::resource('add_payment_info', CarOwnerPaymentInfoController::class);


    //--------------------------------edit car--------------------------------------------------------------------
    Route::post('add_car', [CarsController::class, 'store'])->name('add_car');
    Route::post('update_car/{id}', [CarsController::class, 'update'])->name('update_car');
    Route::delete('delete_car/{id}', [CarsController::class, 'destroy'])->name('delete_car');

    Route::get('get_my_car/{status}', [CarsController::class, 'get_my_car'])->name('get_my_car');
});

//------------------------------------car renter------------------------------------------------------------------


Route::middleware('auth:car_renters')->group(function () {});
