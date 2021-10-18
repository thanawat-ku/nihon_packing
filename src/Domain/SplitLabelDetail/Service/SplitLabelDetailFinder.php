<?php

namespace App\Domain\SplitLabelDetail\Service;

use App\Domain\SplitLabelDetail\Repository\SplitLabelDetailRepository;

/**
 * Service.
 */
final class SplitLabelDetailFinder
{
    private $repository;

    public function __construct(SplitLabelDetailRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findSplitLabelDetails(array $params): array
    {
        return $this->repository->findSplitLabelDetail($params);
    }

    public function findSplitLabelDetailsForscan(array $params): array
    {
        return $this->repository->findSplitLabelDetailsForscan($params);
    }
}
