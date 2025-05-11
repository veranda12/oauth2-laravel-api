<?php

namespace App\Services;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class LinkedInOpenIDProvider extends AbstractProvider
{
    protected $scopes = [
        'openid',
        'profile',
        'email'
    ];

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://www.linkedin.com/oauth/v2/authorization', $state);
    }

    protected function getTokenUrl()
    {
        return 'https://www.linkedin.com/oauth/v2/accessToken';
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://api.linkedin.com/v2/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'     => $user['sub'] ?? null,
            'name'   => $user['name'] ?? ($user['given_name'] ?? '') . ' ' . ($user['family_name'] ?? ''),
            'email'  => $user['email'] ?? $user['email_verified'] ?? null,
            'avatar' => $user['picture'] ?? null,
        ]);
    }
}
