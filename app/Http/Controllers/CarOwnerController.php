<?php

namespace App\Http\Controllers;

use App\Models\car_owner;
use App\Models\cars;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class CarOwnerController extends Controller
{



    public function index()
    {
        $car_owners = car_owner::with('cars', 'cars.imgs')->get();
        return response()->json([
            'car_owners' => $car_owners
        ]);
    }


    public function store(Request $request)
    {

        try {

            $user = car_owner::where('email', $request->email)->first();


            if ($user->email_verified_at == null) {
                $user->forcedelete();
            }



            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                // بيانات أساسية
                'email' => 'required|email|unique:car_owners,email',
                'phone' => 'required|string|unique:car_owners,phone',
                'password' => 'required|string',
                'role' => 'required|in:person,Companies',
                'status' => 'required|in:active,inactive',

                // صورة (اختيارية)
                'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

                // عضوية
                'membership_id' => 'nullable|exists:memberships,id',

                // بيانات الشركات
                'legal_name' => 'nullable|string|max:255',
                'employee_id_number' => 'nullable|string|max:50',
                'vat_number' => 'nullable|string|max:50',
                'head_office_address' => 'nullable|string|max:255',

                // بيانات الأفراد
                'first_name' => 'nullable|string|max:100',
                'last_name' => 'nullable|string|max:100',
                'date_of_birth' => 'nullable|string',

                // بيانات إضافية
                'address' => 'nullable|string|max:255',
                'address2' => 'nullable|string|max:255',
                'zip_code' => 'nullable|string|max:20',
                'city' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',



                'notice_before_trip' => 'nullable|string|max:100',
                'min_duration_trip' => 'nullable|string|max:100',
                'max_duration_trip' => 'nullable|string|max:100',


                'img' => 'nullable|array',
                'img.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:50000',



                'categories_cars_id' => 'nullable|exists:categories_cars,id',
                'car_location' => 'nullable|string|max:255',
                'car_vin' => 'nullable|string|max:17',
                'car_model' => 'nullable|string|max:255',
                'car_mileage_range' => 'nullable|string|max:50',
                'transmission' => 'nullable|in:manual,auto',
                'mechanical_condition' => 'nullable|in:Excellent,Good,Fair,Not_work',
                'all_seats_seatable' => 'nullable|in:yes,no',
                'additional_info' => 'nullable|string',
                'number_of_door' => 'nullable|string|max:255',
                'number_of_seats' => 'nullable|string|max:255',
                'features' => 'nullable|array',
                'description' => 'nullable|string',
                'license_plate_number' => 'nullable|string',
                'state' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $request['password'] = hash::make($request->password);

            $request['features'] = json_encode($request->features);

            $request['otp'] = rand(100000, 999999);

            $car_owner = car_owner::create($request->all());

            $data = $request->all();

            $data['car_owner_id'] = $car_owner->id;


            $car = cars::create($data);




            if ($request->hasFile('img')) {
                $images = $request->file('img');
                foreach ($images as $image) {
                    $path = $image->store('images/cars', 'public');

                    $car->imgs()->create([
                        'car_id' => $car->id,
                        'img' => $path,
                    ]);
                }
            }

            DB::commit();

            $otp = $car_owner->otp;
            Mail::to($request->email)->send(new OTPMail($otp, 'test'));
            return response()->json([
                'message' => 'Car owner created successfully and OTP sent to email successfully',
                'car_owner' => $car_owner,
                'car_info' => $car
            ]);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json(['message' => 'Failed to create car owner' . $e], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $carOwner = car_owner::find($id);

        if (!$carOwner) {
            return response()->json(['message' => 'Car owner not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|email|unique:car_owners,email,' . $carOwner->id,
            'phone' => 'sometimes|unique:car_owners,phone,' . $carOwner->id,
            'password' => 'sometimes|string',
            'role' => 'sometimes|in:person,Companies',
            'status' => 'sometimes|in:active,inactive',

            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'membership_id' => 'nullable|exists:memberships,id',
            'legal_name' => 'nullable|string|max:255',
            'employee_id_number' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'head_office_address' => 'nullable|string|max:255',

            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',

            'address' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $carOwner->update($data);



        return response()->json([
            'message' => 'Car owner updated successfully',
            'car_owner' => $carOwner,
        ]);
    }


    public function destroy($id)
    {
        $car_owner = car_owner::find($id);

        if (!$car_owner) {
            return response()->json(['message' => 'Car owner not found'], 404);
        }

        DB::beginTransaction();

        try {


            foreach ($car_owner->car->imgs as $img) {
                Storage::disk('public')->delete($img->img);
                $img->delete();
            }

            $car_owner->delete();

            DB::commit();
            return response()->json([
                'message' => 'Car owner deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to delete car owner' . $e], 500);
        }
    }
}
