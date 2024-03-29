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
    public function findLotProduct(array $params): array
    {
        return $this->repository->findLotProduct($params);
    }

    public function findLotNsps(array $params): array
    {
        return $this->repository->findLotNsps($params);
    }

    public function findLotsSingleTalbe(array $params): array
    {
        return $this->repository->findLotsSingleTalbe($params);
    }

    public function getLocalMaxLotId():int
    {
        return $this->repository->getLocalMaxLotId()[0]["max_id"];
    }
    public function getSyncLots($max_id):array
    {
        return $this->repository->getSyncLots($max_id);
    }
    public function findLotsNo(array $params): array
    {
        return $this->repository->findLotsNo($params);
    }
    public function findLotLabels(array $params): array
    {
        return $this->repository->findLotLabels($params);
    }
    public function getCountDate($date_count):int
    {
        return $this->repository->getCountDate($date_count)[0]["count_lot"];
    }
}
