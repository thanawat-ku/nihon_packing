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

    public function findPackRow(int $sellID)
    {
        $sellRow = $this->repository->findPackRow($sellID);
        return $sellRow;
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
