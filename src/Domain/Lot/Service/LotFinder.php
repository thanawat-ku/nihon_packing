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

    public function findLotsSingleTalbe(array $params): array
    {
        return $this->repository->findLotsSingleTalbe($params);
    }

    public function getLocalMaxLotId():int
    {
        return $this->repository->getLocalMaxLotId()[0]["max_id"];
    }
    public function getSyncLots():array
    {
        return $this->repository->getSyncLots();
    }
}
