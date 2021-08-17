<?php

namespace App\Domain\SelectProductToMerge\Service;

use App\Domain\SelectProductToMerge\Repository\SelectProductToMergeRepository;

/**
 * Service.
 */
final class SelectProductToMergeFinder
{
    /**
     * @var SelectProductToMergeRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param SelectProductToMergeRepository $repository The repository
     */
    public function __construct(SelectProductToMergeRepository $repository)
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
    public function findSelectProductToMerges(array $params): array
    {
        return $this->repository->findSelectProductToMerges($params);
    }
}
