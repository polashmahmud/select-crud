<?php

namespace App\Http\Controllers;

use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(
                CountryResource::collection(
                    Country::orderBy('name')
                        ->filter($request->only('search'))
                        ->take(10)
                        ->get()
                )
            );
        }

        return 'index page';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $country = Country::create([
            'name' => $request->name
        ]);

        return response()->json(new CountryResource($country));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $country->update([
            'name' => $request->name
        ]);

        return response()->json(new CountryResource($country));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return response()->json(null, 204);
    }
}
