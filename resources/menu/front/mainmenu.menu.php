<?php

declare(strict_types=1);

namespace App\Menu;

use Lyrasoft\Luna\Repository\CategoryRepository;
use Lyrasoft\Luna\Tree\NodeInterface;
use Lyrasoft\Luna\Tree\TreeBuilder;
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

$menu->link('首頁', $nav->to('home'))
    ->icon('fal fa-home');

$menu->link('分類', $nav->to('article_category'))
    ->icon('fal fa-files');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($app, $nav) {
        $categories = $app->service(CategoryRepository::class)
            ->getListSelector()
            ->where('category.state', 1)
            ->where('category.type', 'article')
            ->ordering('category.lft', 'ASC');

        $categoryNodes = TreeBuilder::create($categories);

        $menu->fromTree(
            $categoryNodes,
            function (NodeInterface $node, MenuBuilder $menu) use ($nav) {
                $menu->link(
                    $node->getValue()->title,
                    $nav->to('article_category')->var('path', $node->getValue()->path)
                );
            }
        );
    }
);
