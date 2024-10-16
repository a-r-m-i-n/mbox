<?php

namespace T3\Mbox\Pagination;

use Armin\MboxParser\Mailbox;
use Armin\MboxParser\Parser;
use TYPO3\CMS\Core\Pagination\AbstractPaginator;

final class MboxPaginator extends AbstractPaginator
{
    private Mailbox $paginatedItems;

    public function __construct(
        private readonly string $mboxFilePath,
        int $currentPageNumber = 1,
        int $itemsPerPage = 10,
        private readonly int $total = 0,
    ) {
        $this->setCurrentPageNumber($currentPageNumber);
        $this->setItemsPerPage($itemsPerPage);

        $this->updateInternalState();
    }

    public function getPaginatedItems(): Mailbox
    {
        return $this->paginatedItems;
    }

    protected function updatePaginatedItems(int $itemsPerPage, int $offset): void
    {
        $mboxParser = new Parser();
        $this->paginatedItems = $mboxParser->parse($this->mboxFilePath, $this->getCurrentPageNumber(), $itemsPerPage);
    }

    protected function getTotalAmountOfItems(): int
    {
        return $this->total;
    }

    protected function getAmountOfItemsOnCurrentPage(): int
    {
        return count($this->paginatedItems);
    }
}
