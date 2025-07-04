<?php

namespace App\Http\Controllers;

use App\Models\doos_users;
use App\Models\Permissions;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Traits\HasPermissionCheck;




class DoosUsersController extends Controller
{

    use HasPermissionCheck;

    public function index()
    {

        if ($response = $this->checkPermission('users_mangement', 'view')) {
            return $response;
        }

        $doos_userss = doos_users::all();
        return response()->json([
            'doos_userss' => $doos_userss
        ]);
    }


    public function store(Request $request)
    {

        if ($response = $this->checkPermission('users_mangement', 'add')) {
            return $response;
        }


        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|unique:doos_users,email',
            'password' => 'required',
            'phone' => 'required|unique:doos_users,phone',
            'role' => 'required|in:owner,manager,support',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $request['password'] = hash::make($request->password);

        $doos_users = doos_users::create($request->all());

        return response()->json([
            'message' => 'user created successfully',
            'doos_users' => $doos_users
        ]);
    }


    public function update(Request $request, $id)
    {


        if ($response = $this->checkPermission('users_mangement', 'edit')) {
            return $response;
        }


        $doos_users = doos_users::find($id);

        if (!$doos_users) {
            return response()->json(['message' => 'user not found'], 404);
        }


        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|max:255',
            'email' => 'sometimes|email|unique:doos_userss,email,' . $doos_users->id,
            'password' => 'sometimes',
            'phone' => 'sometimes|unique:doos_userss,phone,' . $doos_users->id,
            'role' => 'sometimes|in:Individuals,Companies',
            'status' => 'sometimes|in:active,inactive',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->password) {
            $request['password'] = hash::make($request->password);
        }

        $doos_users->update($request->all());

        return response()->json([
            'message' => 'user updated successfully',
            'doos_users' => $doos_users
        ]);
    }


    public function destroy($id)
    {

        if ($response = $this->checkPermission('users_mangement', 'delete')) {
            return $response;
        }

        $doos_users = doos_users::find($id);

        if (!$doos_users) {
            return response()->json(['message' => 'user not found'], 404);
        }

        $doos_users->delete();

        return response()->json([
            'message' => 'user deleted successfully'
        ]);
    }
}
