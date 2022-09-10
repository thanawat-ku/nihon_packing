<?php

namespace App\Domain\ReportQtyDif\Service;

use App\Domain\ReportQtyDif\Repository\ReportQtyDifRepository;

/**
 * Service.
 */
final class ReportQtyDifFinder
{
    private $repository;

    public function __construct(ReportQtyDifRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getReportQtyDif(array $params): array
    {
        return $this->repository->getReportQtyDif($params);
    }
}
