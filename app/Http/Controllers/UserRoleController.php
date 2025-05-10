<?php

namespace App\Http\Controllers;

use App\Models\user_role;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Traits\HasPermissionCheck;

class UserRoleController extends Controller
{
    use HasPermissionCheck;

    public function index()
    {
        if ($response = $this->checkPermission('permissions', 'view')) {
            return $response;
        }


        $user_role = user_role::all();
        return response()->json([
            'status' => 'success',
            'user_role' => $user_role
        ]);
    }


    public function store(Request $request)
    {

        if ($response = $this->checkPermission('permissions', 'add')) {
            return $response;
        }


        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:doos_users,id',
            'role_id' => [
                'required',
                Rule::exists('roles', 'id')->whereNull('deleted_at'), // التأكد من أن الدور ليس محذوف
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        $user = user_role::where('user_id', $request->user_id)->whereNull('deleted_at')->first();

        if ($user) {
            return response()->json([
                'status' => 'error',
                'message' => 'user already has a role'
            ], 400);
        }

        $user_role = user_role::create($request->all());

        return response()->json([
            'status' => 'success',
            'user_role' => $user_role
        ]);
    }



    public function update(Request $request, $id)
    {

        if ($response = $this->checkPermission('permissions', 'edit')) {
            return $response;
        }


        $user_role = user_role::find($id);

        if (!$user_role) {
            return response()->json([
                'status' => 'error',
                'message' => 'user_role not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:doos_users,id',
            'role_id' => [
                'required',
                Rule::exists('roles', 'id')->whereNull('deleted_at'), // التأكد من أن الدور ليس محذوف
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        $user_role->update($request->all());

        return response()->json([
            'status' => 'success',
            'user_role' => $user_role
        ]);
    }


    public function destroy($id)
    {
        if ($response = $this->checkPermission('permissions', 'delete')) {
            return $response;
        }


        $user_role = user_role::find($id);

        if (!$user_role) {
            return response()->json([
                'status' => 'error',
                'message' => 'user_role not found'
            ], 404);
        }

        $user_role->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'user_role deleted successfully'
        ]);
    }
}
