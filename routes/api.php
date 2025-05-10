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
use App\Http\Controllers\CategoriesCarsController;
use App\Http\Controllers\RenterCarPaymentInfoController;
use App\Http\Controllers\OrdersCarsController;
use App\Http\Controllers\DriversController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\ReviewsRepalyController;
use App\Http\Controllers\EarningsController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\UserLogController;




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
Route::post('logout', [AuthController::class, 'logout'])->name('logout');



//---------------------public routes-------------------------------------------------------

Route::get('review', [ReviewsController::class, 'index']);

Route::get('repaly', [ReviewsRepalyController::class, 'index']);


Route::get('get_cars', [CategoriesCarsController::class, 'get_cars'])->name('get_cars');
Route::get('categories_cars', [CategoriesCarsController::class, 'index'])->name('categories_cars');
Route::get('get_car_detail/{id}', [CarsController::class, 'get_car_detail'])->name('get_car_detail');
Route::get('get_cars_by_category/{id}', [CarsController::class, 'get_cars_by_category'])->name('get_cars_by_category');

//---------create car owner
Route::post('add_car_owner', [CarOwnerController::class, 'store'])->name('add_car_owner');

//---------create car renter
Route::post('add_car_renter', [CarRenterController::class, 'store'])->name('add_car_renter');

//-------------------------doos users routes--------------------------------------------------------------

Route::middleware('auth:doos_users', 'role:owner')->group(function () {



    //------------------roles && permissions----------------------------------

    Route::resource('roles', RolesController::class);

    Route::get('log', [UserLogController::class, 'index']);


    // Route::get('roles', [RolesController::class, 'index']);
    // Route::post('roles', [RolesController::class, 'store']);
    // Route::post('roles/{id}', [RolesController::class, 'update']);
    // Route::delete('roles/{id}', [RolesController::class, 'destroy']);


    Route::resource('permissions', PermissionsController::class);


    // Route::get('permissions', [PermissionsController::class, 'index']);
    // Route::post('permissions', [PermissionsController::class, 'store']);
    // Route::post('permissions/{id}', [PermissionsController::class, 'update']);
    // Route::delete('permissions/{id}', [PermissionsController::class, 'destroy']);

    Route::resource('user_role', UserRoleController::class);


    // Route::get('user_role', [UserRoleController::class, 'index']);
    // Route::post('user_role', [UserRoleController::class, 'store']);
    // Route::post('user_role/{id}', [UserRoleController::class, 'update']);
    // Route::delete('user_role/{id}', [UserRoleController::class, 'destroy']);

    //-------------------------------------------------------------------------------


    Route::post('update_order_car/{id}', [OrdersCarsController::class, 'update']);

    Route::get('orders_cars', [OrdersCarsController::class, 'index'])->name('orders_cars');


    Route::get('all_users', [DoosUsersController::class, 'index'])->name('all_users');
    Route::post('create_user', [DoosUsersController::class, 'store'])->name('create_user');


    Route::get('get_car_owner', [CarOwnerController::class, 'index'])->name('get_car_owner');

    // resources membership
    Route::resource('membership', MembershipController::class);
});


Route::middleware('auth:doos_users', 'role:manager|owner')->group(function () {});


Route::middleware('auth:doos_users', 'role:support|owner')->group(function () {});

//------------------------------------car owner------------------------------------------------------------------
Route::middleware('auth:car_owners')->group(function () {
    Route::get('earnings_car_owner', [EarningsController::class, 'earnings_car_owner'])->name('earnings_car_owner');



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



Route::middleware('auth:car_renters')->group(function () {


    Route::post('review', [ReviewsController::class, 'store']);
    Route::post('review/{id}', [ReviewsController::class, 'update']);

    Route::delete('review/{id}', [ReviewsController::class, 'destroy']);


    Route::post('rent_car', [OrdersCarsController::class, 'store']);
    Route::post('update_car_renter/{id}', [CarRenterController::class, 'update']);

    Route::delete('delete_car_renter/{id}', [CarRenterController::class, 'destroy']);

    Route::resource('renter_payment_info', RenterCarPaymentInfoController::class);
});

//----------------------------drivers------------------------------------------------------------------------------
Route::post('add_driver', [DriversController::class, 'store'])->name('add_driver');

Route::middleware('auth:drivers')->group(function () {
    Route::get('show_my_reviews', [ReviewsController::class, 'show_my_reviews'])->name('show_my_reviews');
    Route::get('get_orders', [DriversController::class, 'get_orders'])->name('get_orders');
    Route::post('update_driver', [DriversController::class, 'update'])->name('update_driver');
    Route::delete('delete_driver/{id}', [DriversController::class, 'destroy']);

    Route::post('repaly', [ReviewsRepalyController::class, 'store']);
    Route::post('repaly/{id}', [ReviewsRepalyController::class, 'update']);
    Route::delete('repaly/{id}', [ReviewsRepalyController::class, 'destroy']);
});
