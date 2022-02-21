<?php

namespace App\Domain\Packing\Service;

use App\Domain\Packing\Repository\PackingRepository;

/**
 * Service.
 */
final class PackingFinder
{
    /**
     * @var PackingRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param PackingRepository $repository The repository
     */
    public function __construct(PackingRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find cpo_Items.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findPacking(array $params): array
    {
        return $this->repository->findPacking($params);
    }
}
