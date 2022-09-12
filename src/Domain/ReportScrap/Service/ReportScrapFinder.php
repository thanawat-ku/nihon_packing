<?php

namespace App\Domain\ReportScrap\Service;

use App\Domain\ReportScrap\Repository\ReportScrapRepository;

/**
 * Service.
 */
final class ReportScrapFinder
{
    private $repository;

    public function __construct(ReportScrapRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getReportScrap(array $params): array
    {
        return $this->repository->getReportScrap($params);
    }
}
