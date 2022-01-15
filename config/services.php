<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'themoviedb' => [
        'endpoint' => 'https://api.themoviedb.org/3/search/movie',
        'key' => '8ca18e878bf1de4912f387e204ac8c0f',
        'token' => 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4Y2ExOGU4NzhiZjFkZTQ5MTJmMzg3ZTIwNGFjOGMwZiIsInN1YiI6IjYxZTE4ZjI0YTBiNmI1MDAxY2QyMDM3YSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.z7N-nlGsBOt9IN-q9SNtiZ7brtvWRCnEgyiWTxXxWao'
    ]
];
