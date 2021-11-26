<?php

namespace App\Domain\LotDefect\Service;

use App\Domain\LotDefect\Repository\LotDefectRepository;

/**
 * Service.
 */
final class LotDefectFinder
{
    private $repository;

    public function __construct(LotDefectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findLotDefects(array $params): array
    {
        return $this->repository->findLotDefects($params);
    }
}
