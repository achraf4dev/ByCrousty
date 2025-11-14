<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Allowed frontend origins. Keep the dev origin and add the production HTTPS origin
    // to avoid CORS errors when the frontend is served from https://bycrousty.achraf.es.
    // Using '*' together with 'supports_credentials' = true is invalid for browsers,
    // so prefer explicit origins instead of a wildcard.
    'allowed_origins' => [
        'https://bycrousty.achraf.es', // production frontend origin
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Set to true only if you need cookie-based auth (sanctum with cookies).
    // For token based auth (returned token in response) set to false.
    'supports_credentials' => false,

];
