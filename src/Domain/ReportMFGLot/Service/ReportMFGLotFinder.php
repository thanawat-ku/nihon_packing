<?php

namespace App\Domain\ReportMFGLot\Service;

use App\Domain\ReportMFGLot\Repository\ReportMFGLotRepository;

/**
 * Service.
 */
final class ReportMFGLotFinder
{
    private $repository;

    public function __construct(ReportMFGLotRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getReportMFGLot(array $params): array
    {
        return $this->repository->getReportMFGLot($params);
    }
}
