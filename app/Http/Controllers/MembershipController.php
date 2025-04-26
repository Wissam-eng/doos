<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Validator;

use Illuminate\Validation\Rule;


class MembershipController extends Controller
{


    public function index()
    {
        $membership = Membership::where('status', 'active')->get();

        return response()->json([
            'status' => 'success',
            'membership' => $membership
        ]);
    }



    public function store(Request $request)
    {

        $request['user_id'] = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'membership' => 'required|string|max:255|unique:memberships,membership,except,id',
            'role' => 'required|in:person,Companies',
            'cost' => 'required|string|max:255',
            'user_id' => 'required|exists:doos_users,id',
            'limit' => ['required', 'regex:/^unlimited$|^[0-9]+$/']
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $membership = Membership::create($request->all());

        return response()->json([
            'status' => 'success',
            'membership' => $membership
        ]);
    }


    public function update(Request $request, $id)
    {
        $membership = Membership::find($id);

        if (!$membership) {
            return response()->json([
                'status' => 'error',
                'message' => 'Membership not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'membership' => [
                'sometimes',
                'string',
                Rule::unique('memberships', 'membership')->ignore($membership->id),
            ],
            'role' => 'sometimes|in:person,Companies',
            'cost' => 'sometimes|string|max:255',
            'user_id' => 'sometimes|exists:doos_users,id',
            'limit' => ['sometimes', 'regex:/^unlimited$|^[0-9]+$/']
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $membership->update($request->all());

        return response()->json([
            'status' => 'success',
            'membership' => $membership
        ]);
    }

    public function destroy($id)
    {
        $membership = Membership::find($id);

        if (!$membership) {
            return response()->json([
                'status' => 'error',
                'message' => 'Membership not found'
            ], 404);
        }

        $membership->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Membership deleted successfully'
        ]);
    }
}
