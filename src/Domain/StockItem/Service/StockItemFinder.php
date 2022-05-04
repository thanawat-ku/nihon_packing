<?php

namespace App\Domain\StockItem\Service;

use App\Domain\StockItem\Repository\StockItemRepository;

/**
 * Service.
 */
final class StockItemFinder
{
    /**
     * @var StockItemRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param StockItemRepository $repository The repository
     */
    public function __construct(StockItemRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find cpo_Items.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findStockItem(array $params): array
    {
        return $this->repository->findStockItem($params);
    }

}
