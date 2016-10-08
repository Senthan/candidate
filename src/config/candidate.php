<?php

return [
    'secret_key' => env('CANDIDATE_ACCESS_TOKEN', 'YOUR_CANDIDATE_ACCESS_TOKEN'),
    'candidate_api_babe_uri' => env('CANDIDATE_API_BASE_URI', 'https://dd.jeylabs.com'),
    'async_requests' => env('CANDIDATE_ASYNC_REQUESTS', false),
];
