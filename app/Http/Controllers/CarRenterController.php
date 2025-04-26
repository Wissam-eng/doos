<?php

namespace App\Http\Controllers;

use App\Models\car_renter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class CarRenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $car_renters = car_renter::all();
        return response()->json([
            'car_renters' => $car_renters
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|unique:car_renters,email',
            'password' => 'required',
            'phone' => 'required|unique:car_renters,phone',
            'role' => 'required|in:normal,vip',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $request['password'] = hash::make($request->password);

        $car_renter = car_renter::create($request->all());

        return response()->json([
            'message' => 'car renter created successfully',
            'car_renter' => $car_renter
        ]);
    }


    public function update(Request $request, $id)
    {
        $car_renter = car_renter::find($id);

        if (!$car_renter) {
            return response()->json(['message' => 'car renter not found'], 404);
        }


        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|max:255',
            'email' => 'sometimes|email|unique:car_renters,email,' . $car_renter->id,
            'password' => 'sometimes',
            'phone' => 'sometimes|unique:car_renters,phone,' . $car_renter->id,
            'role' => 'sometimes|in:Individuals,Companies',
            'status' => 'sometimes|in:active,inactive',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->password) {
            $request['password'] = hash::make($request->password);
        }

        $car_renter->update($request->all());

        return response()->json([
            'message' => 'car renter updated successfully',
            'car_renter' => $car_renter
        ]);
    }


    public function destroy($id)
    {
        $car_renter = car_renter::find($id);

        if (!$car_renter) {
            return response()->json(['message' => 'car renter not found'], 404);
        }

        $car_renter->delete();

        return response()->json([
            'message' => 'car renter deleted successfully'
        ]);
    }
}
