<?php

namespace App\Http\Controllers;

use App\Models\roles;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Traits\HasPermissionCheck;

class RolesController extends Controller
{
    use HasPermissionCheck;

    public function index()
    {


        if ($response = $this->checkPermission('permissions', 'view')) {
            return $response;
        }


        $roles = roles::where('status', 'active')->get();

        return response()->json([
            'status' => 'success',
            'roles' => $roles
        ]);
    }


    public function store(Request $request)
    {



        if ($response = $this->checkPermission('permissions', 'add')) {
            return $response;
        }




        $validator = Validator::make($request->all(), [
            'role' => [
                'required',
                'string',
                Rule::unique('roles', 'role')->whereNull('deleted_at'),  // التأكد من أن الدور فريد وغير محذوف
            ],
            'membership' => 'required|string',
            'repaly_review' => 'required|string',
            'users_mangement' => 'required|string',
            'financial' => 'required|string',
            'rental' => 'required|string',
            'permissions' => 'required|string',
            'car_owners' => 'required|string',
            'car_renters' => 'required|string',
            'drivers' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $roles = roles::create($request->all());

        return response()->json([
            'status' => 'success',
            'roles' => $roles
        ]);
    }


    public function update(Request $request, $id)
    {

        if ($response = $this->checkPermission('permissions', 'edit')) {
            return $response;
        }


        $roles = roles::find($id);

        if (!$roles) {
            return response()->json([
                'status' => 'error',
                'message' => 'roles not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'role' => [
                'required',
                'string',
                Rule::unique('roles', 'role')->whereNull('deleted_at'),  // التأكد من أن الدور فريد وغير محذوف
            ],
            'membership' => 'sometimes|string',
            'repaly_review' => 'sometimes|string',
            'users_mangement' => 'sometimes|string',
            'financial' => 'sometimes|string',
            'rental' => 'sometimes|string',
            'permissions' => 'sometimes|string',
            'car_owners' => 'sometimes|string',
            'car_renters' => 'sometimes|string',
            'drivers' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $roles->update($request->all());

        return response()->json([
            'status' => 'success',
            'roles' => $roles
        ]);
    }

    public function destroy($id)
    {

        if ($response = $this->checkPermission('permissions', 'delete')) {
            return $response;
        }


        $roles = roles::find($id);

        if (!$roles) {
            return response()->json([
                'status' => 'error',
                'message' => 'roles not found'
            ], 404);
        }

        $roles->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'roles deleted successfully'
        ]);
    }
}
