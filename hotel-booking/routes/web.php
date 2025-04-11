<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.app');
});
// Catch-all route for Vue
Route::get('{any}', function () {
    return view('layouts.app'); // Make sure this is the correct Blade file
})->where('any', '.*'); // Matches any route
