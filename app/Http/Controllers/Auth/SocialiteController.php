<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\UsersLogin;
use Psr\Http\Message\ServerRequestInterface;
use App\Services\AuthService;
use Log;
use Str;

class SocialiteController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function redirectToGoogle()
    {
        Log::info("tst " . config('services.google.redirect'));
        Log::info(env('GOOGLE_REDIRECT_URI'));
        return Socialite::driver('google')
            ->stateless()
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent select_account',
            ])
            ->redirect();
    }

    public function handleGoogleCallback(ServerRequestInterface $serverRequest)
    {
        try {
            $socialite = Socialite::driver('google')
                ->stateless()
                ->setHttpClient(new \GuzzleHttp\Client([
                    'verify' => false,
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ]));

            $googleUser = $socialite->user();

            $user = UsersLogin::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'nama' => $googleUser->getName(),
                    'password' => bcrypt(env('PASSWORD_CLIENT')),
                ]
            );

            // jika ingin menggunakan refresh token personal passport
            $request = new Request([
                'email' => $user->email,
                'password' => env('PASSWORD_CLIENT')
            ]);
            Log::info(json_encode($googleUser));
            $response = $this->authService->attemptLogin($request, $serverRequest);
            $originalContent = json_decode($response->getContent(), true);
            //===================================================================================
            $tokenResult = $user->createToken('access_token');
            $accessToken = $tokenResult->accessToken;
            $refresh_token = $googleUser->refreshToken ?? null;
            // $refreshToken = optional($tokenResult->token)->refresh_token ?? null;  

            return redirect(config('services.frontend.url') . '/auth/callback?token=' . $accessToken . '&refresh_token=' . $refresh_token . '&user_id=' . $user->id);
        } catch (\Exception $th) {
            Log::error('Google callback error: ' . $th->getMessage());
            return redirect('http://localhost:5173/auth/callback?error=google_auth_failed');
        }
    }

    public function redirectToGithub()
    {
        Log::info(config('services.github.redirect'));
        Log::info(env('GITHUB_REDIRECT_URL'));
        return Socialite::driver('github')
            ->stateless()
            ->redirect();
    }

    public function handleGithubCallback()
    {
        try {
            $githubUser = Socialite::driver('github')
                ->stateless()
                ->setHttpClient(new \GuzzleHttp\Client([
                    'verify' => false,
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ]))
                ->user();

            Log::debug('GitHub user data', [
                'id' => $githubUser->getId(),
                'name' => $githubUser->getName(),
                'email' => $githubUser->getEmail(),
                'nickname' => $githubUser->getNickname()
            ]);

            if (!$githubUser->getEmail()) {
                throw new \Exception('Email not provided by GitHub');
            }
            $user = UsersLogin::firstOrCreate(
                ['email' => $githubUser->getEmail()],
                [
                    'nama' => $githubUser->getName() ?? $githubUser->getNickname(),
                    'password' => bcrypt(Str::random(32)),
                    'id' => $githubUser->getId()
                ]
            );

            Log::debug('User after firstOrCreate', [
                'was_created' => $user->wasRecentlyCreated,
                'user_id' => $user->id
            ]);

            $tokenResult = $user->createToken('github_token', ['*']);
            $refreshToken = optional($tokenResult->token)->refresh_token ?? null;

            return redirect(config('services.frontend.url') . '/auth/github/callback?' . http_build_query([
                'token' => $tokenResult->accessToken,
                'user_id' => $user->id,
                'provider' => 'github'
            ]));

            // return redirect(config('services.frontend.url') . '/auth/github/callback?token=' . $accessToken . '&user_id=' . $user->id);
        } catch (\Exception $e) {
            Log::error('GitHub callback failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect('http://localhost:5173/auth/callback?error=google_auth_failed');
        }
    }

    public function redirectToLinkedIn()
    {

        // $url = Socialite::driver('linkedin')->stateless()->redirect();
        // Log::info(config('services.linkedin.redirect', ));
        // Log::info(env('LINKEDIN_REDIRECT_URI'));
        // Log::info($url);
        // return $url;
        return Socialite::driver('linkedin-oidc')->redirect();
    }

    public function handleLinkedInCallback(Request $request)
    {
        Log::info('LinkedIn callback query', $request->all());
        $linkedinUser = Socialite::driver('linkedin-oidc')->stateless()
            ->setHttpClient(new \GuzzleHttp\Client([
                'verify' => false,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]
            ]))
            ->user();
        
        $user = UsersLogin::firstOrCreate(
            ['email' => $linkedinUser->getEmail()],
            [
                'name' => $linkedinUser->getName(),
                'linkedin_id' => $linkedinUser->getId(),
                'avatar' => $linkedinUser->getAvatar(), 
            ]
        );

        $token = $user->createToken('linkedin-token', ['*'])->accessToken;
        return redirect(config('services.frontend.url') . '/auth/callback/linkedin?' . http_build_query([
            'token' => $token,
            'user_id' => $user->id,
            'provider' => 'github'
        ]));
    }
}
