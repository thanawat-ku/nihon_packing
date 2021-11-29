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
    
    public function getLocalMaxDefectId():int
    {
        $data=$this->repository->getLocalMaxDefectId()[0]["max_id"];
        if(is_null($data)){
            return 0;
        }
        else{
            return $data;
        }
    }
    public function getSyncDefects(int $maxId):array
    {
        return $this->repository->getSyncDefects($maxId);
    }
}
