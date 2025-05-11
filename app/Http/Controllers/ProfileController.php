<?php

namespace App\Http\Controllers;

use App\Models\UsersLogin;
use Illuminate\Http\Request;
use App\Helpers\ErrorHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Log;
use Throwable;

class ProfileController extends Controller
{
    public function showByEmail(Request $request)
    {
        Log::info('[TokenFromCookie] Middleware triggered. Cookie: ');

        try {
            $request->validate([
                'email' => 'required|email'
            ]);
            Log::debug('', ['email'=> $request->email]);
            $user = UsersLogin::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User dengan email tersebut tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'msg' => 'Profil berhasil ditemukan.',
                'user' => $user,
            ]);
        } catch (ValidationException $e) {
            return ErrorHandler::handleValidationError($e);
        } catch (AuthenticationException $e) {
            return response()->json([
                'message' => 'Token tidak valid atau tidak diberikan.',
            ], 401);
        }catch (Throwable $e) {
            return ErrorHandler::handleGeneralError($e);
        }
    }
}