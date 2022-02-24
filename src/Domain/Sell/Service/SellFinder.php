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
        $sellRow = $this->repository->findSellRow($sellID);
        return $sellRow;
    }

    public function findSellTag(array $params): array
    {
        return $this->repository->findSellTag($params);
    }
    public function findSellLabel(array $params): array
    {
        return $this->repository->findSellLabel($params);
    }
}
