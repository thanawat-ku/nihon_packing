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
    public function findReportAlls(array $params): array
    {
        return $this->repository->findReportAlls($params);
    }
    
    public function getLocalMaxReportAllId():int
    {
        $data=$this->repository->getLocalMaxReportAllId()[0]["max_id"];
        if(is_null($data)){
            return 0;
        }
        else{
            return $data;
        }
    }
    public function getSyncReportAlls(int $maxId):array
    {
        return $this->repository->getSyncReportAlls($maxId);
    }
}
