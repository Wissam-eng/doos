<?php

namespace App\Http\Controllers;

use App\Models\earnings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EarningsController extends Controller
{

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }


    public function update(Request $request, earnings $earnings)
    {
        //
    }


    public function destroy(earnings $earnings)
    {
        //
    }


    public function earnings_car_owner()
    {
        $id = Auth::user()->id;

        $earnings = Earnings::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(car_owner) as total')
            ->whereHas('order.car.carOwner', function ($query) use ($id) {
                $query->where('car_owner_id', $id);
            })
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->orderByRaw('YEAR(created_at) DESC, MONTH(created_at) DESC')
            ->get();

        return response()->json([
            'status' => 'success',
            'earnings' => $earnings
        ]);
    }
}
