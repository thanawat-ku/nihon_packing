<?php

namespace App\Domain\MergeLabel\Service;

use App\Domain\MergeLabel\Repository\MergeLabelRepository;

/**
 * Service.
 */
final class MergeLabelFinder
{
    private $repository;
    public function __construct(MergeLabelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findMergeLabels(array $params): array
    {
        return $this->repository->findMergeLabels($params);
    }
}
