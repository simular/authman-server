<?php

declare(strict_types=1);

namespace App\Menu;

use Windwalker\Core\Application\AppContext;
use Lyrasoft\Luna\Menu\MenuBuilder;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Language\LangService;

/**
 * @var MenuBuilder $menu
 * @var AppContext $app
 * @var Navigator $nav
 * @var LangService $lang
 */

$menu->header('MENU');

$menu->link('用戶', '#')
    ->icon('fal fa-users-gear');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($nav, $lang) {
        // User
        $menu->link($lang('unicorn.title.grid', title: $lang('luna.user.title')))
            ->to($nav->to('user_list'))
            ->icon('fal fa-users');
    }
);

$menu->link('內容管理', '#')
    ->icon('fal fa-pen-ruler');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($nav, $lang) {
        // Category
        $menu->link($lang('luna.article.category.list'))
            ->to($nav->to('category_list', ['type' => 'article']))
            ->icon('fal fa-sitemap');

        // Article
        $menu->link($lang('unicorn.title.grid', title: $lang('luna.article.title')))
            ->to($nav->to('article_list'))
            ->icon('fal fa-newspaper');

        // Page
        $menu->link($lang('unicorn.title.grid', title: $lang('luna.page.title')))
            ->to($nav->to('page_list'))
            ->icon('fal fa-files');

        // Tag
        $menu->link($lang('unicorn.title.grid', title: $lang('luna.tag.title')))
            ->to($nav->to('tag_list'))
            ->icon('fal fa-tags');
    }
);

// Banner
$menu->link('橫幅管理')
    ->to($nav->to('banner_list'))
    ->icon('fal fa-gallery-thumbnails');

// Widget
$menu->link($lang('unicorn.title.grid', title: $lang('luna.widget.title')))
    ->to($nav->to('widget_list'))
    ->icon('fal fa-shapes');

// Menu
$menu->link($lang('luna.menu.manager.title', title: $lang('luna.menu.type.mainmenu')))
    ->to($nav->to('menu_list', ['type' => 'mainmenu']))
    ->icon('fal fa-list');

// Contact
$menu->link($lang('contact.list.title', title: $lang('contact.main.title')))
    ->to($nav->to('contact_list', ['type' => 'main']))
    ->icon('fal fa-phone-volume');

// Configs
$menu->link('設定檔', '#')
    ->icon('fal fa-cogs');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($nav, $lang) {
        // Config Core
        $menu->link($lang('luna.config.title', $lang('luna.config.type.core')))
            ->to($nav->to('config_core'))
            ->icon('fal fa-cog');
    }
);
