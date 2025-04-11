<?php

namespace App\Http\Controllers;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class AmenitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Amenity::all(), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:amenities,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $amenity = Amenity::create([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Amenity added successfully', 'amenity' => $amenity], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $amenity = Amenity::find($id);

        if (!$amenity) {
            return response()->json(['message' => 'Amenity not found'], 404);
        }

        return response()->json($amenity, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:amenities,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $amenity = Amenity::find($id);

        if (!$amenity) {
            return response()->json(['message' => 'Amenity not found'], 404);
        }

        $amenity->update([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Amenity updated successfully', 'amenity' => $amenity], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $amenity = Amenity::find($id);

        if (!$amenity) {
            return response()->json(['message' => 'Amenity not found'], 404);
        }

        $amenity->delete();

        return response()->json(['message' => 'Amenity deleted successfully'], 200);
    }
}
