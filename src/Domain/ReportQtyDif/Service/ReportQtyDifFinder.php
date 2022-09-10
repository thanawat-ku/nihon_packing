<?php

namespace App\Domain\ReportPacksNotSell\Service;

use App\Domain\ReportPacksNotSell\Repository\ReportPacksNotSellRepository;

/**
 * Service.
 */
final class ReportPacksNotSellFinder
{
    private $repository;

    public function __construct(ReportPacksNotSellRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getReportQtyDifFinder(array $params): array
    {
        return $this->repository->getReportQtyDifFinder($params);
    }
}