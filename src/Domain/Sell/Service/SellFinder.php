<?php

namespace App\Domain\Sell\Service;

use App\Domain\Sell\Repository\SellRepository;

/**
 * Service.
 */
final class SellFinder
{
    /**
     * @var SellRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param SellRepository $repository The repository
     */
    public function __construct(SellRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find sells.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findSells(array $params): array
    {
        return $this->repository->findSells($params);
    }

    public function findSellProductID(int $product_id)
    {
        // Input validation
        // ...

        // Fetch data from the database
        $userRow = $this->repository->findSellProductID($product_id);

        // Optional: Add or invoke your complex business logic here
        // ...

        // Optional: Map result
        // ...

        return $userRow;
    }

   

    
}
