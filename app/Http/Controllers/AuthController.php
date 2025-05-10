<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Mail;

use App\Models\car_owner;
use App\Models\car_renter;
use App\Models\doos_users;
use App\Models\user_log;


use App\Mail\OTPMail;

class AuthController extends Controller
{
    public function login(Request $request)
    {


        $data = $request->only('email', 'password', 'user_type');

        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required',
            'user_type' => 'required|in:car_owner,car_renter,doos_user,drivers',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $guards = [
            'doos_user' => 'doos_users',
            'car_owner' => 'car_owners',
            'car_renter' => 'car_renters',
            'drivers' => 'drivers',
        ];

        $guard = $guards[$data['user_type']] ?? null;

        if (!$guard) {
            return response()->json(['error' => 'Invalid user type'], 400);
        }

        if ($token = Auth::guard($guard)->attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = Auth::guard($guard)->user();

            // تحقق من التفعيل فقط لملاك ومؤجري السيارات
            if (in_array($guard, ['car_owners', 'car_renters', 'drivers']) && is_null($user->email_verified_at)) {
                return response()->json(['error' => 'Please verify your email'], 401);
            }





            if ($guard === 'doos_users') {
                $log = user_log::create([
                    'user_id' => $user->id,
                    'action' => 'login',
                ]);
            }

            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

    public function me()
    {
        return response()->json(JWTAuth::user());
    }

    public function logout()
    {

        $user = Auth::guard('doos_users')->user();




        if ($user) {
            $log = user_log::create([
                'user_id' => $user->id,
                'action' => 'logout',
            ]);
        }

        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }





    //---------------------------API RESET PASSWORD & VERIFY EMAIL----------------------------------------

    public function sendOTP(Request $request)
    {

        $otp = rand(100000, 999999);

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'user_type' => 'required|in:car_owner,car_renter,doos_user'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'حدث خطاء اثناء التسجيل: ' . $validator->errors(),
                'status' => false
            ]);
        }

        if ($request->user_type == 'car_owner') {
            $user = car_owner::where('email', $request->email)->orWhere('phone', $request->phone)->first();
        } elseif ($request->user_type == 'car_renter') {
            $user = car_renter::where('email', $request->email)->orWhere('phone', $request->phone)->first();
        } elseif ($request->user_type == 'doos_user') {
            $user = doos_users::where('email', $request->email)->orWhere('phone', $request->phone)->first();
        }

        if (!$user) {
            return response()->json([
                'message' => 'حدث خطاء اثناء التسجيل: ' . $validator->errors(),
                'status' => false
            ]);
        }



        $user->update(['otp' => $otp]);


        $data['otp'] = $otp;

        Mail::to($request->email)->send(new OTPMail($otp, 'test'));

        return response()->json([
            'message' => 'تم ارسال الكود بنجاح',
            'status' => true
        ]);
    }


    public function receiveOTP(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
            'user_type' => 'required|in:car_owner,car_renter,doos_user',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'حدث خطاء اثناء التسجيل: ' . $validator->errors(),
                'status' => false
            ]);
        }

        $otp_user = $request->otp;

        if ($request->user_type == 'car_owner') {
            $user = car_owner::where('otp', $otp_user)->first();
        } elseif ($request->user_type == 'car_renter') {
            $user = car_renter::where('otp', $otp_user)->first();
        } elseif ($request->user_type == 'doos_user') {
            $user = doos_users::where('otp', $otp_user)->first();
        }

        if (!$user) {
            return response()->json([
                'message' => 'الكود غير صحيح',
                'status' => false
            ]);
        }


        return response()->json([
            'message' => 'تم التحقق من الكود بنجاح',
            'status' => true
        ]);
    }


    public function resetpassword(Request $request)
    {

        $validator = validator($request->all(), [
            'password' => 'required|confirmed',
            'otp' => 'required',
            'user_type' => 'required|in:car_owner,car_renter,doos_user',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'حدث خطاء اثناء التسجيل: ' . $validator->errors(),
                'status' => false
            ]);
        }

        if ($request->user_type == 'car_owner') {

            $user = car_owner::where('otp', $request->otp);
        } elseif ($request->user_type == 'car_renter') {

            $user = car_renter::where('otp', $request->otp);
        } elseif ($request->user_type == 'doos_user') {

            $user = doos_users::where('otp', $request->otp);
        }


        if (!$user) {
            return response()->json([
                'message' => 'حدث خطاء اثناء التسجيل: ' . $validator->errors(),
                'status' => false
            ]);
        }


        $user->update([

            'password' => Hash::make($request->password),
            'otp' => null
        ]);


        return response()->json([
            'message' => 'تم تغيير كلمة المرور بنجاح',
            'status' => true
        ]);
    }

    public function verfiy_email(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
            'user_type' => 'required|in:car_owner,car_renter,doos_user',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'حدث خطاء اثناء التسجيل: ' . $validator->errors(),
                'status' => false
            ]);
        }

        $otp_user = $request->otp;



        if ($request->user_type == 'car_owner') {
            $user = car_owner::where('otp', $otp_user)->first();
        } elseif ($request->user_type == 'car_renter') {
            $user = car_renter::where('otp', $otp_user)->first();
        } elseif ($request->user_type == 'doos_user') {
            $user = doos_users::where('otp', $otp_user)->first();
        }



        if (!$user) {
            return response()->json([
                'message' => 'الكود غير صحيح',
                'status' => false
            ]);
        }




        $user->update([
            'email_verified_at' => now(),
            'otp' => null
        ]);

        return response()->json([
            'message' => 'تم التحقق من الكود بنجاح',
            'status' => true
        ]);
    }
}
