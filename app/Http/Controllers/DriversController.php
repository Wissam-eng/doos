<?php

namespace App\Http\Controllers;

use App\Models\drivers;
use App\Models\orders_cars;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriversController extends Controller
{


    public function index()
    {
        $drivers = drivers::all();
        return response()->json([
            'status' => 'success',
            'drivers' => $drivers
        ]);
    }


    public function get_orders()
    {
        $order_car = orders_cars::where('driver_id', Auth::user()->id)->where('status', 'pending')->get();

        return response()->json([
            'status' => 'success',
            'order_car' => $order_car
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|unique:drivers,phone,except,id',
            'addres' => 'required|string',
            'email' => 'required|email|unique:drivers,email',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ]);
        }

        $request['password'] = hash::make($request->password);

        $drivers = drivers::create($request->all());

        return response()->json([
            'status' => 'success',
            'drivers' => $drivers
        ]);
    }

    public function update(Request $request)
    {
        $drivers = drivers::find(Auth::user()->id);

        if (!$drivers) {
            return response()->json([
                'status' => 'error',
                'message' => 'drivers not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|unique:drivers,phone,except,id',
            'addres' => 'sometimes|string',

            'email' => [
                'sometimes',
                'email',
                Rule::unique('drivers', 'email')->ignore($drivers->id),
            ],


            'password' => 'sometimes|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ]);
        }

        if ($request->password) {
            $request['password'] = hash::make($request->password);
        }

        $drivers->update($request->all());

        return response()->json([
            'status' => 'success',
            'drivers' => $drivers
        ]);
    }


    public function destroy($id)
    {
        $drivers = drivers::find($id);

        if (!$drivers) {
            return response()->json([
                'status' => 'error',
                'message' => 'drivers not found'
            ], 404);
        }

        $drivers->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'drivers deleted successfully'
        ]);
    }
}
