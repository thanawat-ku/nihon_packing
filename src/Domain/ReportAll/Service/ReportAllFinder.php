<?php

namespace App\Domain\ReportAll\Service;

use App\Domain\ReportAll\Repository\ReportAllRepository;

/**
 * Service.
 */
final class ReportAllFinder
{
    private $repository;

    public function __construct(ReportAllRepository $repository)
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
    public function findReportAll(array $params): array
    {
        return $this->repository->findReportAll($params);
    }
}
