<?php

namespace App\Domain\Label\Service;

use App\Domain\Label\Repository\LabelRepository;

/**
 * Service.
 */
final class LabelFinder
{
    private $repository;
    
    public function __construct(LabelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findLabels(array $params): array
    {
        return $this->repository->findLabels($params);
    }
    public function findLabelForLotZero(array $params): array
    {
        return $this->repository->findLabelForLotZero($params);
    }
    public function findLabelsForScan(array $params): array
    {
        return $this->repository->findLabelsForScan($params);
    }
    public function findLabelForMerge(array $params): array
    {
        return $this->repository->findLabelForMerge($params);
    }
    public function findLabelForMergeLotZero(array $params): array
    {
        return $this->repository->findLabelForMergeLotZero($params);
    }
    public function findLabelNonfullys(array $params): array
    {
        return $this->repository->findLabelNonfullys($params);
    }
}

final class LabelPackMergeFinder
{
    private $repository;
    public function __construct(LabelRepository $repository)
    {
        $this->repository = $repository;
    }

    // public function findLabelPackMerges(array $params): array
    // {
    //     return $this->repository->findLabelPackMerges($params);
    // }
}
