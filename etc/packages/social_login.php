<?php

declare(strict_types=1);

use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Luna\User\Handler\UserHandler;
use Lyrasoft\Luna\User\Handler\UserHandlerInterface;

return [
    'social_login' => [
        'social_providers' => [
            'Facebook' => [
                'enabled' => false,
                'keys' => [
                    'id' => env('FACEBOOK_SOCIAL_ID'),
                    'secret' => env('FACEBOOK_SOCIAL_SECRET'),
                ],
                'scope' => 'email',
                'profile_handler' => \Lyrasoft\Luna\Auth\Profile\FacebookProfileHandler::class
            ],
            'Google' => [
                'enabled' => false,
                'keys' => [
                    'id' => env('GOOGLE_SOCIAL_ID'),
                    'secret' => env('GOOGLE_SOCIAL_SECRET'),
                ],
                'scope' => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
                'profile_handler' => \Lyrasoft\Luna\Auth\Profile\GoogleProfileHandler::class
            ],
            'LINE' => [
                'enabled' => false,
                'adapter' => \Lyrasoft\Luna\Auth\Provider\LineSocialProvider::class,
                'keys' => [
                    'id' => env('LINE_SOCIAL_ID'),
                    'secret' => env('LINE_SOCIAL_SECRET'),
                ],
                // 'scope' => '',
                // 'profile_handler' => \App\Social\LineProfileHandler::class
            ],
        ]
    ]
];
