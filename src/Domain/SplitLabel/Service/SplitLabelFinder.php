<?php

namespace App\Domain\SplitLabel\Service;

use App\Domain\SplitLabel\Repository\SplitLabelRepository;

/**
 * Service.
 */
final class SplitLabelFinder
{
    private $repository;

    public function __construct(SplitLabelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findSplitLabels(array $params): array
    {
        return $this->repository->findSplitLabels($params);
    }
}
