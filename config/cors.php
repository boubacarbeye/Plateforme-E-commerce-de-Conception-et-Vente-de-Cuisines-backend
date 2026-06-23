<?php

return [
    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:5173'], // ← ton frontend Vite

    'allowed_headers' => ['*'],

    'supports_credentials' => false,
];