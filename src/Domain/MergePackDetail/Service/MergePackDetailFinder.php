<?php

namespace App\Domain\MergePackDetail\Service;

use App\Domain\MergePackDetail\Repository\MergePackDetailRepository;

/**
 * Service.
 */
final class MergePackDetailFinder
{
    private $repository;
    public function __construct(MergePackDetailRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findMergePackDetailFromLots(array $params): array
    {
        return $this->repository->findMergePackDetailFromLots($params);
    }

    public function findMergePackDetailFromMergePacks(array $params): array
    {
        return $this->repository->findMergePackDetailFromMergePacks($params);
    }

    public function findMergePackDetails(array $params): array
    {
        return $this->repository->findMergePackDetails($params);
    }

    public function findMergePackDetailForRegisters(array $params): array
    {
        return $this->repository->findMergePackDetailForRegisters($params);
    }
    
    public function findMergePackDetailsForMerge(array $params): array
    {
        return $this->repository->findMergePackDetailsForMerge($params);
    }
}
