<?php

namespace App\Domain\CreateMergeNoFromLabel\Service;

use App\Domain\CreateMergeNoFromLabel\Repository\CreateMergeNoFromLabelRepository;

/**
 * Service.
 */
final class CreateMergeNoFromLabelFinder
{
    private $repository;
    public function __construct(CreateMergeNoFromLabelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findCreateMergeNoFromLabels(array $params): array
    {
        return $this->repository->findCreateMergeNoFromLabels($params);
    }

    public function findMergePacks(array $params): array
    {
        return $this->repository->findMergePacks($params);
    }
}

