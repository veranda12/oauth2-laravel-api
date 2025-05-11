<?php

namespace App\Services;

use Illuminate\Http\Request;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Support\Facades\App;

class AuthService
{
    public function attemptLogin($request, ServerRequestInterface $serverRequest)
    {
        $data = $request->only(['email', 'password']);

        $serverRequest = $serverRequest->withParsedBody([
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_CLIENT_ID'),
            'client_secret' => env('PASSPORT_CLIENT_SECRET'),
            'username' => $data['email'],
            'password' => $data['password'],
            'scope' => '',
        ]);

        return App::make(AccessTokenController::class)->issueToken($serverRequest);
    }
}
