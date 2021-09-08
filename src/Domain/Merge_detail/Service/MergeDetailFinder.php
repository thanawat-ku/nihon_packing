<?php

namespace App\Domain\Merge_detail\Service;

use App\Domain\Merge_detail\Repository\MergeDetailRepository;

/**
 * Service.
 */
final class MergeDetailFinder
{
    /**
     * @var MergeDetailRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param MergeDetailRepository $repository The repository
     */
    public function __construct(MergeDetailRepository $repository)
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