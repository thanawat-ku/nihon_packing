<?php

namespace App\Domain\MergePack\Service;

use App\Domain\MergePack\Repository\MergePackRepository;

/**
 * Service.
 */
final class MergePackFinder
{
    private $repository;

    public function __construct(MergePackRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find customers.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findMergePacks(array $params): array
    {
        return $this->repository->findMergePacks($params);
    }

    public function findLabelPackMerges(array $params): array
    {
        return $this->repository->findLabelPackMerges($params);
    }

    public function findPackMergeFromProductID(array $params): array
    {
        return $this->repository->findPackMergeFromProductID($params);
    }
}
