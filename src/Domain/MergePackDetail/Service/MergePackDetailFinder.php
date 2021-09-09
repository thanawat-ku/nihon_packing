<?php

namespace App\Domain\MergePackDetail\Service;

use App\Domain\MergePackDetail\Repository\MergePackDetailRepository;

/**
 * Service.
 */

final class LabelNonfullyFinder
{
    private $repository;
    public function __construct(MergePackDetailRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findLabelNonfullys(array $params): array
    {
        return $this->repository->findLabelNonfullys($params);
    }
}

final class MergePackDetailFinder
{
    private $repository;
    public function __construct(MergePackDetailRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findMergePackDetails(array $params): array
    {
        return $this->repository->findMergePackDetails($params);
    }

    public function findLabelNonfullys(array $params): array
    {
        return $this->repository->findLabelNonfullys($params);
    }

    public function findLabelNonfully(array $params): array
    {
        return $this->repository->findLabelNonfully($params);
    }
}




