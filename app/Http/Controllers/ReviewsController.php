<?php

namespace App\Http\Controllers;

use App\Models\reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ReviewsController extends Controller
{


    public function index()
    {
        $reviews = reviews::with('repaly')->get();

        return response()->json([
            'status' => 'success',
            'reviews' => $reviews
        ]);
    }


    public function show_my_reviews()
    {

        $reviews = reviews::with('order' , 'repaly')->whereHas('order', function ($query) {
            $query->where('driver_id', Auth::user()->id);
        })->get();

        return response()->json([
            'status' => 'success',
            'reviews' => $reviews
        ]);
    }


    public function store(Request $request)
    {
        $request['renter_id'] = Auth::user()->id;


        // dd(Auth::user()->id);
        $validator = Validator::make($request->all(), [
            'renter_id' => 'required|exists:car_renters,id',
            'order_id' => 'required|exists:orders_cars,id',
            'comment' => 'nullable',
            'rate' => 'required|in:1,2,3,4,5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        $reviews = reviews::create($request->all());

        return response()->json([
            'status' => 'success',
            'reviews' => $reviews
        ]);
    }


    public function update(Request $request, $id)
    {
        $review = reviews::find($id);

        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'Review not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'renter_id' => 'sometimes|exists:car_renters,id',
            'order_id' => 'sometimes|exists:orders_cars,id',
            'comment' => 'sometimes',
            'rate' => 'sometimes|in:1,2,3,4,5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        $review->update($request->all());

        return response()->json([
            'status' => 'success',
            'reviews' => $review
        ]);
    }


    public function destroy($id)
    {
        $review = reviews::find($id);

        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'Review not found'
            ], 404);
        }

        $review->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Review deleted successfully'
        ]);
    }
}
