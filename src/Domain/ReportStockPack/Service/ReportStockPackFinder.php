<?php

namespace App\Domain\ReportStockPack\Service;

use App\Domain\ReportStockPack\Repository\ReportStockPackRepository;

/**
 * Service.
 */
final class ReportStockPackFinder
{
    private $repository;

    public function __construct(ReportStockPackRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getReportStockPack(array $params): array
    {
        return $this->repository->getReportStockPack($params);
    }
}
