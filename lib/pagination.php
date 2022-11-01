<?php


namespace Bx\Model;


use Bx\Model\Interfaces\ModelQueryInterface;
use Bx\Model\Interfaces\Models\PaginationInterface;
use Bx\Model\Interfaces\QueryInterface;

class Pagination implements PaginationInterface
{
    /**
     * @var ModelQueryInterface
     */
    private $query;
    /**
     * @var int
     */
    private $totalCount;
    /**
     * @var int
     */
    private $countPages;

    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
    }

    public function getPage(): int
    {
        return $this->query->getPage();
    }

    public function getCountPages(): int
    {
        if (isset($this->countPages)) {
            return $this->countPages;
        }

        $limit = $this->getLimit();
        return $this->countPages = ($limit > 0 ? ceil($this->getTotalCountElements()/$limit) : 1);
    }

    public function getTotalCountElements(): int
    {
        if (isset($this->totalCount)) {
            return $this->totalCount;
        }

        return $this->totalCount = $this->query->getTotalCount();
    }

    public function getCountElements(): int
    {

        $page = $this->getPage();
        $countPages = $this->getCountPages();
        if ($page > $countPages) {
            return 0;
        }

        $totalCount = $this->getTotalCountElements();
        $limit = $this->getLimit();
        if ($limit === 0) {
            return $totalCount;
        }

        if ($page === $countPages) {
            return $totalCount - $limit * ($countPages-1);
        }

        return $totalCount > $limit ? $limit : $totalCount;
    }

    public function getLimit(): int
    {
        return $this->query->getLimit();
    }
    
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'currentPage' => $this->getPage(),
            'itemsCount' => $this->getTotalCountElements(),
            'itemsCountOnPage' => $this->getCountElements(),
            'pagesCount' => $this->getCountPages(),
            'itemsPerPage' => $this->getLimit()
        ];
    }
}
