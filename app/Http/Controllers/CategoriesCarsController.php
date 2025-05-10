<?php

namespace App\Http\Controllers;

use App\Models\categories_cars;
use Illuminate\Http\Request;

class CategoriesCarsController extends Controller
{


    public function index()
    {
        $cat_car = categories_cars::all();

        return response()->json([
            'status' => 'success',
            'categories_cars' => $cat_car
        ]);

    }


    public function get_cars()
    {
        $cars = categories_cars::with('car')->get();
        return response()->json([
            'status' => 'success',
            'cars' => $cars
        ]);
    }



    public function store(Request $request)
    {
        //
    }


    public function update(Request $request, categories_cars $categories_cars)
    {
        //
    }


    public function destroy(categories_cars $categories_cars)
    {
        //
    }
}
