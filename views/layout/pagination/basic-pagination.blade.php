<?php

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $pagination \Windwalker\Core\Pagination\Pagination
 * @var $result     \Windwalker\Core\Pagination\PaginationResult
 */

declare(strict_types=1);

namespace App\View;

use Windwalker\Core\Pagination\Pagination;

/**
 * @var $pagination Pagination
 */
$result = $pagination->compile();
$current = $result->getCurrent();

$mobileNeighbours = 2;
?>

<nav aria-label="navigation">
    <ul class="pagination c-pagination">
        @if ($first = $result->getFirst())
            <li class="page-item page-item--first">
                <a class="page-link" href="{{ $first }}">
                    <i class="fa-solid fa-backward-step"></i>
                    {{--<span class="sr-only">First</span>--}}
                </a>
            </li>
        @endif

        @if ($previous = $result->getPrevious())
            <li class="page-item page-item--previous">
                <a class="page-link" href="{{ $previous }}">
                    <i class="fa-solid fa-backward"></i>
                    {{--<span class="sr-only">Previous</span>--}}
                </a>
            </li>
        @endif

        @if ($less = $result->getLess())
            <li class="page-item page-item--less d-none d-md-block">
                <a class="page-link" href="{{ $less }}">
                    Less
                </a>
            </li>
        @endif

        @foreach ($result->getPages() as $k => $page)
                <?php
                $active = $page->name === Pagination::CURRENT;
                $offset = $page->page - $current->page;
                $distanceClass = abs($offset) > $mobileNeighbours ? 'd-none d-md-block' : '';
                ?>
            <li class="page-item page-item--{{ $page->page }} {{ $active ? 'active' : $distanceClass }}"
                data-offset="{{ $offset }}"
                data-page="{{ $page->page }}"
            >
                <a class="page-link" href="{{ $active ? 'javascript://' : $page }}">
                    {{ $page->page }}
                </a>
            </li>
        @endforeach

        @if ($more = $result->getMore())
            <li class="page-item page-item--more d-none d-md-block">
                <a class="page-link" href="{{ $more }}">
                    More
                </a>
            </li>
        @endif

        @if ($next = $result->getNext())
            <li class="page-item page-item--next">
                <a class="page-link" href="{{ $next }}">
                    {{--<span class="sr-only">Next</span>--}}
                    <i class="fa-solid fa-forward"></i>
                </a>
            </li>
        @endif

        @if ($last = $result->getLast())
            <li class="page-item page-item--last">
                <a class="page-link" href="{{ $last }}">
                    {{--<span class="sr-only">Last</span>--}}
                    <i class="fa-solid fa-forward-step"></i>
                </a>
            </li>
        @endif
    </ul>
</nav>
