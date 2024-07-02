<?php

namespace App\Http\Controllers;

use App\Http\Resources\DistrictResource;
use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(
                DistrictResource::collection(
                    District::orderBy('name')
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

        $district = District::create([
            'name' => $request->name
        ]);

        return response()->json(new DistrictResource($district));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, District $district)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $district->update([
            'name' => $request->name
        ]);

        return response()->json(new DistrictResource($district));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(District $district)
    {
        $district->delete();

        return response()->json(null, 204);
    }
}
