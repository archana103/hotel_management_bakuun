<?php

namespace App\Http\Controllers;
use App\Models\Hotel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\HotelImage;
use Illuminate\Support\Facades\Storage;
class HotelController extends Controller
{
    public function search(Request $request)
    {
        $query = Hotel::query();
    
        // Location filter
        if ($request->filled('location')) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
        }
    
        // Room availability (check-in & check-out)
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $query->whereHas('rooms', function ($q) use ($request) {
                $q->whereDoesntHave('bookings', function ($subQ) use ($request) {
                    $subQ->where('status', '!=', 'cancelled')
                        ->where(function ($conflict) use ($request) {
                            $conflict->where('check_in', '<', $request->check_out)
                                     ->where('check_out', '>', $request->check_in);
                        });
                });
            });
        }
        
    
        // Min & Max Price filter
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('rooms', function ($q) use ($request) {
                if ($request->filled('min_price')) {
                    $q->where('price', '>=', $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', $request->max_price);
                }
            });
        }
    
        // Amenities filter
        if ($request->filled('amenities')) {
            $query->whereHas('amenities', function ($q) use ($request) {
                $q->whereIn('amenities.id', $request->amenities);
            });
        }
    
        // Guests filter
        if ($request->filled('guests')) {
            $query->whereHas('rooms', function ($q) use ($request) {
                $q->where('capacity', '>=', $request->guests);
            });
        }
    
        $hotels = $query->with(['rooms', 'amenities', 'images'])->get();
    
        return response()->json([
            'message' => 'Hotels fetched successfully',
            'hotels' => $hotels
        ], 200);
    }
    
    public function index()
    {
        $hotels = Hotel::with(['rooms', 'amenities', 'images'])->orderBy('created_at', 'desc')->paginate(10); // Adjust per page
        return response()->json($hotels, 200);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string', // ← add this line
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // ← main image
            'hotel_images' => 'nullable|array', // ← updated to match Vue
            'hotel_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'managers' => 'required|array',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
   
        // Create the hotel
        $hotel = Hotel::create([
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'address' => $request->address,
        ]);

        // Save main image
        if ($request->hasFile('image')) {
            $mainImagePath = $request->file('image')->store('hotel_images', 'public');
            $hotel->image = $mainImagePath;
            $hotel->save();
        }
    
        // Attach managers
        $hotel->managers()->attach($request->managers);
    
        // Attach amenities
        if ($request->has('amenities')) {
            $hotel->amenities()->sync($request->amenities);
        }
    
        // Handle additional images
        if ($request->hasFile('hotel_images')) {
            foreach ($request->file('hotel_images') as $image) {
                $imagePath = $image->store('hotel_images', 'public');
                $hotel->images()->create(['image_path' => $imagePath]);
            }
        }
    
        return response()->json([
            'message' => 'Hotel created successfully',
            'hotel' => $hotel->load(['managers', 'amenities', 'images']),
        ], 201);
    }
    


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json(Hotel::with(['managers', 'amenities', 'images','rooms'])->findOrFail($id), 200);
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
        $hotel = Hotel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('image')) {
            $hotel->image = $request->file('image')->store('hotels', 'public');
        }

        $hotel->update($request->only(['name', 'location', 'description']));

        if ($request->has('amenities')) {
            $hotel->amenities()->sync($request->amenities);
        }

        return response()->json([
            'message' => 'Hotel updated successfully',
            'hotel' => $hotel->load('amenities'),
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $hotel = Hotel::findOrFail($id);
    
            
            $hotel->rooms()->delete();
            $hotel->images()->delete();
    
            $hotel->delete();
    
            return response()->json(['message' => 'Hotel deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete hotel.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getHotelRooms($hotelId)
    {
        $hotel = Hotel::with('rooms')->findOrFail($hotelId);
    
        return response()->json($hotel->rooms, 200);
    } 
    public function uploadMainImage(Request $request, Hotel $hotel)
{
    $request->validate([
        'main_image' => 'required|image|max:2048',
    ]);
    $path = $request->file('main_image')->store('hotel_images', 'public');


    $hotel->image = $path;
    $hotel->save();

    return response()->json(['message' => 'Main image updated.',"imagename"=>$hotel->image],200);
}

public function uploadGalleryImages(Request $request, Hotel $hotel)
{
    $request->validate([
        'images.*' => 'image|max:2048',
    ]);

    foreach ($request->file('images') as $image) {
        $path = $image->store('hotel_images', 'public');

        $hotel->images()->create([
            'image_path' => $path,
        ]);
    }

    return response()->json(['message' => 'Gallery images uploaded.']);
}

public function deleteGalleryImage($id)
{
    $image = HotelImage::findOrFail($id);

    Storage::delete($image->image_path);
    $image->delete();

    return response()->json(['message' => 'Image deleted.']);
}
}
