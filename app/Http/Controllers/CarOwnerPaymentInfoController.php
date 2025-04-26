<?php

namespace App\Http\Controllers;

use App\Models\car_owner_payment_info;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CarOwnerPaymentInfoController extends Controller
{

    public function index()
    {
        $user_id = Auth::user()->id;
        $car_owner_payment_info = car_owner_payment_info::where('car_owner_id', $user_id)->get();
        return response()->json([
            'status' => 'success',
            'car_owner_payment_info' => $car_owner_payment_info
        ]);
    }


    public function store(Request $request)
    {

        $request['car_owner_id'] = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'car_owner_id' => 'required|exists:car_owners,id',
            'name_on_card' => 'required|max:255',
            'card_number' => 'required|max:255',
            'expiration_date' => 'required|max:255',
            'cvv' => 'required|max:255',
            'country' => 'required|max:255',
            'address_line_1' => 'required|max:255',
            'address_line_2' => 'nullable|max:255',
            'city' => 'required|max:255',
            'country' => 'required|max:255',
            'zip_code' => 'nullable|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        $car_owner_payment_info = car_owner_payment_info::create($request->all());

        return response()->json([
            'status' => 'success',
            'car_owner_payment_info' => $car_owner_payment_info
        ]);
    }


    public function update(Request $request, $id)
    {
        $payment_info = car_owner_payment_info::find($id);

        if (!$payment_info) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment info not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name_on_card' => 'required|max:255',
            'card_number' => 'required|max:255',
            'expiration_date' => 'required|max:255',
            'cvv' => 'required|max:255',
            'country' => 'required|max:255',
            'address_line_1' => 'required|max:255',
            'address_line_2' => 'nullable|max:255',
            'city' => 'required|max:255',
            'country' => 'required|max:255',
            'zip_code' => 'nullable|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        $payment_info->update($request->all());

        return response()->json([
            'status' => 'success',
            'payment_info' => $payment_info
        ]);
    }


    public function destroy($id)
    {
        $payment_info = car_owner_payment_info::find($id);

        if (!$payment_info) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment info not found'
            ], 404);
        }

        $payment_info->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Payment info deleted successfully'
        ]);
    }
}
