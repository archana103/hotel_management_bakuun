<?php
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AmenitiesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::resource('hotels', HotelController::class);
    Route::resource('rooms', RoomController::class);
    Route::post('/check-availability', [BookingController::class, 'checkAvailability']);
    Route::resource('bookings', BookingController::class);
    Route::put('/user/profile', [UserController::class, 'update']);
    Route::get('/bookings_details/{id}', [BookingController::class, 'show_bookings']);
    Route::put('/booking/{id}/cancel', [BookingController::class, 'cancel_bookings']);
    Route::get('/admins', [AdminController::class, 'index']);
    Route::delete('/admins/{id}', [AdminController::class, 'destroy']);
    Route::post('/register-admin', [AdminController::class, 'create']);
    Route::get('/rooms/{room}/bookings', [BookingController::class, 'getRoomBookings']);
    Route::get('/hotels/{hotelId}/rooms', [HotelController::class, 'getHotelRooms']);
    Route::get('/rooms/{room}/availability', [RoomController::class, 'availability']);
    Route::put('/bookings/{booking}', [BookingController::class, 'update']);
    Route::put('/admins/{id}', [AdminController::class, 'update']);
    Route::post('/hotels/{hotel}/main-image', [HotelController::class, 'uploadMainImage']);
    Route::post('/hotels/{hotel}/images', [HotelController::class, 'uploadGalleryImages']);
    Route::delete('/hotel-images/{id}', [HotelController::class, 'deleteGalleryImage']);
    Route::get('/reports/overview', [ReportController::class, 'overviewReport']);
    Route::get('hotels_manager/{id}', [HotelController::class, 'fetch_hotel_for_managers']);
});
Route::middleware('auth:sanctum')->get('/notifications', function (Request $request) {
    return $request->user()->notifications;
});
Route::resource('amenities', AmenitiesController::class);
Route::post('/hotels/search', [HotelController::class, 'search']);

Route::get('/users', [UserController::class, 'index']);

