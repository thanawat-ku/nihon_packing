<?php

namespace App\Domain\ReportAllExcel\Service;

use App\Domain\ReportAllExcel\Repository\ReportAllExcelRepository;

/**
 * Service.
 */
final class ReportAllExcelFinder
{
    private $repository;

    public function __construct(ReportAllExcelRepository $repository)
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
    public function findReportAllExcel(array $params): array
    {
        return $this->repository->findReportAllExcel($params);
    }
}
