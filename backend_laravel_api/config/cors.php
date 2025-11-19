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
        'http://localhost:5173',       // local Vue dev server
        'http://localhost:3000',       // alternative local port
        'http://127.0.0.1:5173',       // localhost alternative
        'http://89.128.172.67:5173' // local dev via IP (if needed)
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Set to true only if you need cookie-based auth (sanctum with cookies).
    // For token based auth (returned token in response) set to false.
    'supports_credentials' => false,

];
