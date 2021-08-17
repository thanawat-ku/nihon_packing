<?php

namespace App\Domain\LabelPackMerge\Service;

use App\Domain\LabelPackMerge\Repository\LabelPackMergeRepository;

/**
 * Service.
 */

final class LabelPackMergeFinder
{
    private $repository;
    public function __construct(LabelPackMergeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findLabelPackMerges(array $params): array
    {
        return $this->repository->findLabelPackMerges($params);
    }
}
