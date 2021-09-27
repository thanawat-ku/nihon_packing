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
    public function findLabelNonfullys(array $params): array
    {
        return $this->repository->findLabelNonfullys($params);
    }


    public function checklabel(string $labelNO)
    {
        $userRow = $this->repository->checklabel($labelNO);

        return $userRow;
    }

    public function findCreateMergeNoFromLabels(array $params): array
    {
        return $this->repository->findCreateMergeNoFromLabels($params);
    }

    public function findLabelPackMerges(array $params): array
    {
        return $this->repository->findLabelPackMerges($params);
    }
}

final class LabelPackMergeFinder
{
    private $repository;
    public function __construct(LabelRepository $repository)
    {
        $this->repository = $repository;
    }
}
