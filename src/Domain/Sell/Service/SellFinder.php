<?php

namespace App\Domain\Sell\Service;

use App\Domain\Sell\Repository\SellRepository;

/**
 * Service.
 */
final class SellFinder
{
    
    private $repository;

    public function __construct(SellRepository $repository)
    {
        $this->repository = $repository;
    }
    public function findSells(array $params): array
    {
        return $this->repository->findSells($params);
    }

    public function findSellRow(int $sellID)
    {
        
        $userRow = $this->repository->findSellRow($sellID);
        return $userRow;
    }
}
