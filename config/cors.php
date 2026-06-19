<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // routes concernées

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:5173'], // ton frontend Vite

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false, // mettre true si tu utilises des cookies/session
];