<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

return [
    'banner' => [
        'providers' => [
            \Lyrasoft\Banner\BannerPackage::class
        ],
        'widget' => [
            'upload_profile' => 'image'
        ],
        'type_enum' => null,
        'video_enabled' => true,
        'types' => [
            '_default' => [
                'desktop' => [
                    'width' => 1920,
                    'height' => 800,
                    'crop' => true,
                    'ajax' => false,
                    'image_ext' => 'jpg',
                    'profile' => 'image',
                ],
                'mobile' => [
                    'width' => 720,
                    'height' => 720,
                    'crop' => true,
                    'ajax' => false,
                    'image_ext' => 'jpg',
                    'profile' => 'image',
                ]
            ]
        ]
    ]
];
