<?php

namespace App\Domain\MergePack\Service;

use App\Domain\MergePack\Repository\ProductRepository;

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
}