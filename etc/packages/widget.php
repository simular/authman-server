<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Lyrasoft\Banner\Widget\Banner\BannerWidget;
use Lyrasoft\Luna\Widget\Custom\CustomHtmlWidget;

return [
    'widget' => [
        'types' => [
            'custom_html' => CustomHtmlWidget::class,
            'banner' => BannerWidget::class
        ],
        'positions' => [
            'demo' => 'Demo'
        ]
    ]
];
