<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/google/redirect', [SocialiteController::class, 'redirectToGoogle']);
Route::get('/auth/google/callbacks', [SocialiteController::class, 'handleGoogleCallback']);

Route::get('/auth/github/redirect', [SocialiteController::class, 'redirectToGithub']);
Route::get('/auth/github/callbacks', [SocialiteController::class, 'handleGithubCallback']);

Route::get('/auth/linkedin/redirect', [SocialiteController::class, 'redirectToLinkedIn']);
Route::get('/auth/callback/linkedin', [SocialiteController::class, 'handleLinkedInCallback']);