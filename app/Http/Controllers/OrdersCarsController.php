<?php

namespace App\Http\Controllers;

use App\Models\orders_cars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrdersCarsController extends Controller
{

    public function index()
    {
        $orders_cars = orders_cars::with('car')->get();

        return response()->json([
            'status' => 'success',
            'orders_cars' => $orders_cars
        ]);
    }


    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // نصف قطر الأرض بالكيلومترات

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return round($distance, 2); // ترجيع المسافة بالكيلومترات بدقة منزلتين عشريتين
    }


    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'car_id' => 'required|exists:cars,id',
            'renter_id' => 'required|exists:car_renters,id',
            'driver_id' => 'sometimes|exists:drivers,id',
            'latitude_from' => 'required',
            'longitude_from' => 'required',
            'latitude_to' => 'required',
            'longitude_to' => 'required',
            'min_price' => 'required',
            'max_price' => 'required',
            'driver' => 'required|in:with_driver,without_driver',
            'contract_file' => 'sometimes|mimetypes:application/pdf',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $distance = $this->calculateDistance($request->latitude_from, $request->longitude_from, $request->latitude_to, $request->longitude_to);

        $request['distance'] = $distance;
        $request['price'] = $request->min_price;


        $order = orders_cars::create($request->all());

        return response()->json([
            'status' => 'success',
            'order' => $order
        ]);
    }

    public function update(Request $request, $id)
    {
        $orders_cars = orders_cars::findOrFail($id);

        if (!$orders_cars) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'car_id' => 'sometimes|exists:cars,id',
            'renter_id' => 'sometimes|exists:car_renters,id',
            'driver_id' => 'sometimes|exists:drivers,id',
            'latitude_from' => 'sometimes',
            'longitude_from' => 'sometimes',
            'latitude_to' => 'sometimes',
            'longitude_to' => 'sometimes',
            'min_price' => 'sometimes',
            'max_price' => 'sometimes',
            'driver' => 'sometimes|in:with_driver,without_driver',
            'contract_file' => 'sometimes|mimetypes:application/pdf',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        if ($request->latitude_from && $request->longitude_from && $request->latitude_to && $request->longitude_to) {
            $distance = $this->calculateDistance($request->latitude_from, $request->longitude_from, $request->latitude_to, $request->longitude_to);

            $request['actual_distance'] = $distance;

            if ($distance > $orders_cars->distance) {
                $request['extra_distance'] = $distance - $orders_cars->distance;
            }
        }

        // تحديث الطلب
        $orders_cars->update($request->all());

        return response()->json([
            'status' => 'success',
            'order' => $orders_cars
        ]);
    }

    public function destroy($id)
    {
        $orders_cars = orders_cars::findOrFail($id);

        if (!$orders_cars) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);
        }
        // حذف الطلب
        $orders_cars->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Order deleted successfully'
        ]);
    }
}
