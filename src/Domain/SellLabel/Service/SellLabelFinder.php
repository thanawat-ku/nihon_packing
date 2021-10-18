<?php

namespace App\Domain\SellLabel\Service;

use App\Domain\SellLabel\Repository\SellLabelRepository;

/**
 * Service.
 */
final class SellLabelFinder
{
    /**
     * @var SellLabelRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param SellLabelRepository $repository The repository
     */
    public function __construct(SellLabelRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find sell_labels.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findSellLabels(array $params): array
    {
        return $this->repository->findSellLabels($params);
    }

    public function findSellLabelForLots(array $params): array
    {
        return $this->repository->findSellLabelForLots($params);
    }
    public function findSellLabelForMergePacks(array $params): array
    {
        return $this->repository->findSellLabelForMergePacks($params);
    }
}
