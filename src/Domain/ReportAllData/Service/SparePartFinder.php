<?php

namespace App\Domain\SparePart\Service;

use App\Domain\SparePart\Repository\SparePartRepository;

/**
 * Service.
 */
final class SparePartFinder
{
    /**
     * @var SparePartRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param SparePartRepository $repository The repository
     */
    public function __construct(SparePartRepository $repository)
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
    public function findSpareParts(array $params): array
    {
        return $this->repository->findSpareParts($params);
    }
    /**
     * Find customers.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findNotInLayoutSpareParts(array $params): array
    {
        return $this->repository->findNotInLayoutSpareParts($params);
    }
}
