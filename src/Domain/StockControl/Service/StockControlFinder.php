<?php

namespace App\Domain\StockControl\Service;

use App\Domain\StockControl\Repository\StockControlRepository;

/**
 * Service.
 */
final class StockControlFinder
{
    /**
     * @var StockControlRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param StockControlRepository $repository The repository
     */
    public function __construct(StockControlRepository $repository)
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
    public function findStockControl(array $params): array
    {
        return $this->repository->findStockControl($params);
    }

    public function findStockControlSelect(array $rtdata): array
    {
        return $this->repository->findStockControlSelect($rtdata);
    }

    public function findIDFromProductName(array $params, int $ProductID)
    {
        return  $this->repository->findIDFromProductName($params, $ProductID);

        // return $userRow;
    }
}
