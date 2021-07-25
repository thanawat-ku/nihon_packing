<?php

namespace App\Domain\Lot\Service;

use App\Domain\Lot\Repository\LotRepository;

/**
 * Service.
 */
final class LotFinder
{
    private $repository;
    public function __construct(LotRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findLots(array $params): array
    {
        return $this->repository->findLots($params);
    }
}
