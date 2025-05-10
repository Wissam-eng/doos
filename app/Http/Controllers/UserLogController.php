<?php

namespace App\Http\Controllers;

use App\Models\user_log;
use Illuminate\Http\Request;

use App\Traits\HasPermissionCheck;

class UserLogController extends Controller
{

    use HasPermissionCheck;

    public function index()
    {

        if ($response = $this->checkPermission('log', 'view')) {
            return $response;
        }



        $log = user_log::all();
        return response()->json([
            'status' => 'success',
            'log' => $log
        ]);
    }


    public function store(Request $request)
    {
        //
    }


    public function update(Request $request, user_log $user_log)
    {
        //
    }


    public function destroy()
    {
        //
    }
}
