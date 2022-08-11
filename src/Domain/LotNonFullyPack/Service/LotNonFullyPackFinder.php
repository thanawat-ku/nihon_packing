<?php

namespace App\Domain\LotNonFullyPack\Service;

use App\Domain\LotNonFullyPack\Repository\LotNonFullyPackRepository;

/**
 * Service.
 */
final class LotNonFullyPackFinder
{
    private $repository;

    public function __construct(LotNonFullyPackRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find customers.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findLotNonFullyPacks(array $params): array
    {
        return $this->repository->findLotNonFullyPacks($params);
    }
    public function checkLabelInLotNonFullyPacks(array $params): array
    {
        return $this->repository->checkLabelInLotNonFullyPacks($params);
    }
}
