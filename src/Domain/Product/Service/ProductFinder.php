<?php

namespace App\Domain\Product\Service;

use App\Domain\Product\Repository\ProductRepository;

/**
 * Service.
 */
final class ProductFinder
{
    /**
     * @var ProductRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param ProductRepository $repository The repository
     */
    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find products.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findProducts(array $params): array
    {
        return $this->repository->findProducts($params);
    }
    // public function findIDFromProductName(array $params): array
    // {
    //     return $this->repository->findIDFromProductName($params);
    // }

    public function findIDFromProductName(string $ProductName)
    {
        $userRow = $this->repository->findIDFromProductName($ProductName);

        return $userRow;
    }

    public function findProductForSells(array $params): array
    {
        return $this->repository->findProductForSells($params);
    }

    
}
