<?php

namespace App\Domain\TempQuery\Service;

use App\Domain\TempQuery\Repository\TempQueryRepository;

/**
 * Service.
 */
final class TempQueryFinder
{
    private $repository;
    public function __construct(TempQueryRepository $repository)
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
    public function findTempQuery(array $params): array
    {
        return $this->repository->findTempQuery($params);
    }
    public function findTempQueryCheck(array $params): array
    {
        return $this->repository->findTempQueryCheck($params);
    }
    public function findTempQueryCheckUpdate(array $params): array
    {
        return $this->repository->findTempQueryCheckUpdate($params);
    }
}
