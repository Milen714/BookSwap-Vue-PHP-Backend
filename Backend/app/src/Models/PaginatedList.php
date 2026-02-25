<?php 
namespace App\Models;

class PaginatedList {
    public array $items;
    public int $currentPage;
    public int $totalPages;
    public int $itemsPerPage;
    public int $totalItems;
    public bool $hasNextPage;
    public bool $hasPreviousPage;
    public int $firstItemIndex;
    public int $lastItemIndex;

    public function __construct(array $items, int $currentPage, int $itemsPerPage, int $totalItems) {
        $this->items = $items;
        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
        $this->totalItems = $totalItems;
        $this->totalPages = (int) ceil($totalItems / $itemsPerPage);
        $this->hasNextPage = $this->hasNextPage();
        $this->hasPreviousPage = $this->hasPreviousPage();
        $this->calculateItemIndices();
    }
    private function hasPreviousPage(): bool {
        return $this->currentPage > 1;
    }

    private function hasNextPage(): bool {
        return $this->currentPage < $this->totalPages;
    }
    private function calculateItemIndices(): void {
        $this->firstItemIndex = ($this->currentPage - 1) * $this->itemsPerPage + 1;
        $this->lastItemIndex = min($this->firstItemIndex + count($this->items) - 1, $this->totalItems);
    }

    public function createPaginatedList(array $allItems, int $currentPage, int $itemsPerPage): PaginatedList {
        $totalItems = count($allItems);
        $offset = ($currentPage - 1) * $itemsPerPage;
        $items = array_slice($allItems, $offset, $itemsPerPage);
        return new PaginatedList($items, $currentPage, $itemsPerPage, $totalItems);
    }


    
    
}   