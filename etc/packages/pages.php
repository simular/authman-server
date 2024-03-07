<?php

declare(strict_types=1);

use Lyrasoft\Luna\LunaPackage;
use Lyrasoft\Luna\PageBuilder\Addon\Button\ButtonAddon;
use Lyrasoft\Luna\PageBuilder\Addon\Emptyspace\EmptyspaceAddon;
use Lyrasoft\Luna\PageBuilder\Addon\Feature\FeatureAddon;
use Lyrasoft\Luna\PageBuilder\Addon\Image\ImageAddon;
use Lyrasoft\Luna\PageBuilder\Addon\Text\TextAddon;
use Windwalker\Core\Asset\AssetService;

return [
    'pages' => [
        'addons' => [
            'text' => TextAddon::class,
            'image' => ImageAddon::class,
            'button' => ButtonAddon::class,
            'feature' => FeatureAddon::class,
            'emptyspace' => EmptyspaceAddon::class,
        ],
        'page_extends' => [
            'global.body' => 'luna.page.extends.global.body'
        ],
        'protects' => [
            'theme.'
        ],
        'upload_profile' => 'image',
        'styles' => [
            'font_size_unit' => 'rem',
            'font_size_scale' => 0.0625
        ],
        'templates' => [
            function (AssetService $asset) {
                return [
                    'title' => 'Page',
                    'type' => 'page',
                    'description' => 'Basic page template',
                    'image' => $asset->handleUri('@luna/images/admin/page/templates/tmpl-a.jpg'),
                    'file' => LunaPackage::path('resources/pages/tmpl-a.json')
                ];
            }
        ]
    ],
];
