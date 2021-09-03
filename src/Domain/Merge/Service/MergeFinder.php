<?php

namespace App\Domain\Merge\Service;

use App\Domain\Merge\Repository\MergeRepository;

/**
 * Service.
 */
final class MergeFinder
{
    /**
     * @var MergeRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param MergeRepository $repository The repository
     */
    public function __construct(MergeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find products.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findMerges(array $params): array
    {
        return $this->repository->findMerges($params);
    }
}
