<?php

namespace App\Service;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class Paginator
{
    private $knpPaginator;

    public function __construct(PaginatorInterface $knpPaginator)
    {
        $this->knpPaginator = $knpPaginator;
    }

    public function getPaginatedData($phones, int $page, int $limit, Request $request): ?array
    {
        // pagination base on knp Paginator
        $paginated = $this->knpPaginator->paginate(
            $phones,
            $page, /*page number*/
            $limit,/*limit per page*/
        );

        // getting some data generated from knp Paginator
        $items = $paginated->getItems();

        // if there is no result, we return null
        if (empty($items)) {
            return null;
        }

        $itemsPerPage = $paginated->getItemNumberPerPage();
        $totalCount = $paginated->getTotalItemCount();

        // generating previous and next links if possible
        $previousPage = $nextPage = null;
        if ($page > 1) {
            $previousPage = ($page - 1);
            $previousPageLink = $request->getUriForPath($request->getPathInfo().'?page='.$previousPage);
        }
        $totalPages = ceil($totalCount / $limit);
        if ($page < $totalPages) {
            $nextPage = ($page + 1);
            $nextPageLink = $request->getUriForPath($request->getPathInfo().'?page='.$nextPage);
        }

        // validating user input
        if ($page > $totalPages) {
            return ['message' => 'invalid page number'];
        }

        return [
            'current_page_number' => $page,
            'number_items_per_page' => $itemsPerPage,
            'total_items_count' => $totalCount,
            'previous_page_link' => (null !== $previousPage) ? $previousPageLink : null,
            'next_page_link' => (null !== $nextPage) ? $nextPageLink : null,
            'items' => $items,
        ];
    }
}
