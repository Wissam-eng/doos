<?php

namespace App\Http\Controllers;

use App\Models\reviews_repaly;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;

use App\Traits\HasPermissionCheck;


class ReviewsRepalyController extends Controller
{

    use HasPermissionCheck;

    public function index()
    {
        if ($response = $this->checkPermission('repaly_review', 'view')) {
            return $response;
        }

        $repaly = reviews_repaly::all();
        return response()->json([
            'status' => 'success',
            'repaly' => $repaly
        ]);
    }

    public function store(Request $request)
    {

        if ($response = $this->checkPermission('repaly_review', 'add')) {
            return $response;
        }



        $request['driver_id'] = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'review_id' => 'required|exists:reviews,id',
            'driver_id' => 'required|exists:drivers,id',
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        $repaly = reviews_repaly::create($request->all());

        return response()->json([
            'status' => 'success',
            'repaly' => $repaly
        ]);
    }


    public function update(Request $request, $id)
    {

        if ($response = $this->checkPermission('repaly_review', 'edit')) {
            return $response;
        }


        $repaly = reviews_repaly::find($id);

        if (!$repaly) {
            return response()->json([
                'status' => 'error',
                'message' => 'repaly not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'review_id' => 'sometimes|exists:reviews,id',
            'driver_id' => 'sometimes|exists:drivers,id',
            'comment' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        $repaly->update($request->all());

        return response()->json([
            'status' => 'success',
            'repaly' => $repaly
        ]);
    }

    public function destroy($id)
    {

        if ($response = $this->checkPermission('repaly_review', 'delete')) {
            return $response;
        }


        $repaly = reviews_repaly::find($id);

        if (!$repaly) {
            return response()->json([
                'status' => 'error',
                'message' => 'repaly not found'
            ], 404);
        }

        $repaly->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'repaly deleted successfully'
        ]);
    }
}
