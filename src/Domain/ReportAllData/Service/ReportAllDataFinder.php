<?php

namespace App\Domain\ReportAllData\Service;

use App\Domain\ReportAllData\Repository\ReportAllDataRepository;

/**
 * Service.
 */
final class ReportAllDataFinder
{
    private $repository;

    public function __construct(ReportAllDataRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getReportAllData(array $params): array
    {
        return $this->repository->getReportAllData($params);
    }
}
