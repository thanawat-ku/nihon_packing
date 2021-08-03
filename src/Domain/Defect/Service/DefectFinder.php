<?php

namespace App\Domain\Defect\Service;

use App\Domain\Defect\Repository\DefectRepository;

/**
 * Service.
 */
final class DefectFinder
{
    private $repository;

    public function __construct(DefectRepository $repository)
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
    public function findDefects(array $params): array
    {
        return $this->repository->findDefects($params);
    }
}
