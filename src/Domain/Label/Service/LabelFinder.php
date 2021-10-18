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

    public function findCheckLabels(array $params): array
    {
        return $this->repository->findLabels($params);
    }
    public function findLabelSingleTable(array $params): array
    {
        return $this->repository->findLabelSingleTable($params);
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

    public function findLabelCreateFromMerges(array $params): array
    {
        return $this->repository->findLabelCreateFromMerges($params);
    }
    public function findLabelFromMergePacks(array $params): array
    {
        return $this->repository->findLabelFromMergePacks($params);
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
