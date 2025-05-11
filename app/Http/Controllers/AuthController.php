<?php

namespace App\Http\Controllers;

use App\Models\UsersLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ErrorHandler;
use Illuminate\Validation\ValidationException;
use app\Helpers\StrictValidator;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use App\Services\AuthService;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function register(Request $request)
    {
        try {
            Log::debug('Data:', ['data' => $request]);

            StrictValidator::ensureOnlyAllowed($request->all(), ['nama', 'email', 'password']);
            $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users_login,email',
                'password' => 'required|string|min:6',
            ]);

            $user = UsersLogin::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('access_token')->accessToken;

            return response()->json([
                'msg' => 'Registrasi berhasil',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (ValidationException $e) {
            return ErrorHandler::handleValidationError($e);
        } catch (Throwable $e) {
            return ErrorHandler::handleGeneralError($e);
        }

    }

    public function login(Request $request, ServerRequestInterface $serverRequest)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);
            $response = $this->authService->attemptLogin($request, $serverRequest);
            $originalContent = json_decode($response->getContent(), true);
            $accessToken = $originalContent['access_token'] ?? null;
            if (!$accessToken) {
                return response()->json(['message' => 'Gagal mendapatkan access token'], 500);
            }

            $user = UsersLogin::where('email', $request->email)->first();

            return response()->json([
                ...$originalContent,
                'user' => [
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'message' => 'berhasil login!'
                ]
            ], $response->getStatusCode())->cookie(

                    'access_token',
                    $accessToken,
                    60,
                    '/',
                    null,
                    true,
                    true,
                    false,
                    'None'
                );

        } catch (ValidationException $e) {
            return ErrorHandler::handleValidationError($e);
        } catch (Throwable $e) {
            return ErrorHandler::handleGeneralError($e);
        }
    }

    public function issueToken(Request $request)
    {
        $request->validate([
            'grant_type' => 'required|string',
        ]);

        $response = Http::asForm()->post(config('services.passport.login_endpoint'), [
            'grant_type' => $request->grant_type,
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'username' => $request->username,
            'password' => $request->password,
            'refresh_token' => $request->refresh_token,
            'scope' => '',
        ]);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Failed to get token',
                'errors' => $response->json()
            ], 422);
        }

        return response()->json($response->json());
    }
}
