<?php

namespace App\Http\Controllers;

use App\Models\cars;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Traits\HasPermissionCheck;


class CarsController extends Controller
{

    use HasPermissionCheck;


    public function index()
    {

        if (in_array(Auth::user()->role, ['owner', 'manager', 'support'])) {
            if ($response = $this->checkPermission('car_owners', 'view')) {
                return $response;
            }
        }

        $cars = cars::all();

        return response()->json([
            'status' => 'success',
            'cars' => $cars
        ]);
    }



    public function get_cars_by_category($id)
    {
        if (in_array(Auth::user()->role, ['owner', 'manager', 'support'])) {
            if ($response = $this->checkPermission('car_owners', 'view')) {
                return $response;
            }
        }

        $cars = cars::where('categories_cars_id', $id)->get();
        return response()->json([
            'status' => 'success',
            'cars' => $cars
        ]);
    }




    public function get_my_car($status)
    {

        $user  = Auth::user()->id;

        $cars = cars::where(['status' => $status, 'car_owner_id' =>  $user])->get();

        return response()->json([
            'status' => 'success',
            'cars' => $cars
        ]);
    }


    public function get_car_detail($id)
    {

        if (in_array(Auth::user()->role, ['owner', 'manager', 'support'])) {
            if ($response = $this->checkPermission('car_owners', 'view')) {
                return $response;
            }
        }


        $car_detail = cars::with('imgs', 'driver', 'carOwner')->find($id);

        if (!$car_detail) {

            return response()->json([
                'status' => 'false',
                'message' => 'the car not found'
            ]);
        }
        return response()->json([
            'status' => 'success',
            'car_detail' => $car_detail
        ]);
    }

    public function store(Request $request)
    {



        if (in_array(Auth::user()->role, ['owner', 'manager', 'support'])) {
            if ($response = $this->checkPermission('car_owners', 'add')) {
                return $response;
            }
        }


        try {

            $request['car_owner_id'] = Auth::user()->id;

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'car_owner_id' => 'required|exists:car_owners,id',
                'categories_cars_id' => 'required|exists:categories_cars,id',
                'car_location' => 'required|string|max:255',
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

                'img' => 'nullable|array',
                'img.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:50000',

            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ]);
            }

            if (is_array($request->features ?? null)) {
                $request['features'] = json_encode($request->features);
            }

            $car = cars::create($request->all());


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

            return response()->json([
                'status' => 'success',
                'message' => 'Car created successfully',
                'car' => $car
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create car',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {

        if (in_array(Auth::user()->role, ['owner', 'manager', 'support'])) {
            if ($response = $this->checkPermission('car_owners', 'edit')) {
                return $response;
            }
        }


        $car = cars::find($id);

        if (!$car) {
            return response()->json([
                'status' => 'error',
                'message' => 'Car not found'
            ], 404);
        }


        $validator = Validator::make($request->all(), [
            'car_owner_id' => 'nullable|exists:car_owners,id',
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

            'img' => 'nullable|array',
            'img.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:50000',


        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        DB::beginTransaction();

        try {

            $car->update($request->all());


            // لو فيه صور جديدة مرفوعة
            if ($request->hasFile('img')) {
                // حذف الصور القديمة من التخزين وقاعدة البيانات
                foreach ($car->imgs as $img) {
                    Storage::disk('public')->delete($img->img);
                    $img->delete();
                }

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
            return response()->json([
                'status' => 'success',
                'message' => 'Car updated successfully',
                'car' => $car
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update car',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {


        if (in_array(Auth::user()->role, ['owner', 'manager', 'support'])) {
            if ($response = $this->checkPermission('car_owners', 'delete')) {
                return $response;
            }
        }

        $car = cars::with('imgs')->find($id);

        if (!$car) {
            return response()->json([
                'status' => 'error',
                'message' => 'Car not found'
            ], 404);
        }

        DB::beginTransaction();

        try {
            // حذف الصور من التخزين
            foreach ($car->imgs as $img) {
                Storage::disk('public')->delete($img->img);
                $img->delete();
            }

            $car->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Car deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete car',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
