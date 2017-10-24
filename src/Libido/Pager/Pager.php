<?php

namespace Libido\Pager;

class Pager
{
    private $page;
    private $nbEntries;
    private $itemsPerPage;

    /**
     * @param int $page
     * @param int $nbEntries
     * @param int $itemsPerPage
     */
    public function __construct($page, $nbEntries, $itemsPerPage)
    {
        $this->page = $page;
        $this->nbEntries = $nbEntries;
        $this->itemsPerPage = $itemsPerPage;
        if ($page > $this->getLastPage()) {
            $this->page = $this->getLastPage();
        }
        if ($page < 1) {
            $this->page = 1;
        }
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getNbEntries()
    {
        return $this->nbEntries;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @return int
     */
    public function getMinEntry()
    {
        $minEntry = (($this->page - 1) * $this->itemsPerPage) + 1;
        return ($minEntry < 0) ? 0 : $minEntry;
    }

    /**
     * @return int
     */
    public function getMaxEntry()
    {
        $maxEntry = $this->page * $this->itemsPerPage;
        return ($maxEntry > $this->nbEntries) ? $this->nbEntries : $maxEntry;
    }

    /**
     * @return int
     */
    public function getFirstPage()
    {
        return 1;
    }

    /**
     * @return int
     */
    public function getLastPage()
    {
        return (int) (($this->nbEntries + $this->itemsPerPage - 1) / $this->itemsPerPage);
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        $offset = ($this->page - 1) * ($this->itemsPerPage );
        return ($offset < 0) ? 0 : $offset;
    }

    /**
     * @return int
     */
    public function getNextPage()
    {
        return ($this->page >= $this->getLastPage()) ? $this->getLastPage() : $this->page + 1;
    }

    /**
     * @return int
     */
    public function getPreviousPage()
    {
        return ($this->page <= $this->getFirstPage()) ? $this->getFirstPage() : $this->page - 1;
    }

    /**
     * @return bool
     */
    public function hasNextPage()
    {
        return $this->page !== $this->getLastPage();
    }

    /**
     * @return bool
     */
    public function hasPreviousPage()
    {
        return $this->page !== $this->getFirstPage();
    }
}