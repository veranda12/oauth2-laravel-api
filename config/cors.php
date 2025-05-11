<?php

return [

    'paths' => ['api/*','api/profile-by-email', 'login', 'logout', 'sanctum/csrf-cookie','*','auth/google/redirect','/auth/google/callback'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:5173'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
