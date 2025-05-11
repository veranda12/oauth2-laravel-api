<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::post('/oauth/token', [AccessTokenController::class, 'issueToken'])
    ->middleware(['api', 'throttle']);

 
Route::get('/test', function(){
    return response()->json(['msg'=> 'berhasil hit api']);
});

Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login'])->name('login');
Route::post('/oauth/token', [AuthController::class, 'issueToken']);
// Route::middleware('auth:api')->post('/profile-by-email', [ProfileController::class, 'showByEmail']);
Route::middleware(['token.from.cookie','auth:api'])->post('/profile-by-email', [ProfileController::class, 'showByEmail']);

// Route::middleware(['token.from.cookie', 'auth:api'])->post('/profile-by-email', function(Request $request) {
//     Log::info('Reached /profile-by-email route');
// });
