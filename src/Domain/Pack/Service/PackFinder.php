<?php

namespace App\Domain\Pack\Service;

use App\Domain\Pack\Repository\PackRepository;

/**
 * Service.
 */
final class PackFinder
{
    
    private $repository;

    public function __construct(PackRepository $repository)
    {
        $this->repository = $repository;
    }
    public function findPacks(array $params): array
    {
        return $this->repository->findPacks($params);
    }
    public function findPackInvoices(array $params): array
    {
        return $this->repository->findPackInvoices($params);
    }
    public function findPackRow(int $packID)
    {
        $packRow = $this->repository->findPackRow($packID);
        return $packRow;
    }

    public function findPackTag(array $params): array
    {
        return $this->repository->findPackTag($params);
    }
    public function findPackLabel(array $params): array
    {
        return $this->repository->findPackLabel($params);
    }
}
