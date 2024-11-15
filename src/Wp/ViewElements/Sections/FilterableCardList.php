<?php


namespace Torpedo\Wp\ViewElements\Sections;

use Torpedo\Wp\ViewElements\CardList;

class FilterableCardList extends CardList
{
    /** @var Pagination */
    protected $pagination;

    /** @var Filter[] */
    protected $filters = [];

    protected $template = 'elements/sections/filterable-card-list';

    /**
     * return FilterableCardList
     */
    public static function create()
    {
        return new FilterableCardList();
    }

    /**
     * @return Filter[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param Filter[] $filters
     * @return FilterableCardList
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @param mixed $pagination
     * @return FilterableCardList
     */
    public function setPagination(Pagination $pagination)
    {
        $this->pagination = $pagination;
        return $this;
    }
}

