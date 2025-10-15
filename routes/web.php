<?php

use Illuminate\Support\Facades\Route;

// Home page with new layout
Route::get('/', function () {
    return view('home');
});

// Test route to verify Tailwind CSS v4 is working
Route::get('/test', function () {
    return view('test-tailwind');
});

// Simple CSS test
Route::get('/test-simple', function () {
    return view('test-simple');
});
