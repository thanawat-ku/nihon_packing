<?php

namespace App\Domain\SellCpoItem\Service;

use App\Domain\SellCpoItem\Repository\SellCpoItemRepository;

/**
 * Service.
 */
final class SellCpoItemFinder
{
    /**
     * @var SellCpoItemRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param SellCpoItemRepository $repository The repository
     */
    public function __construct(SellCpoItemRepository $repository)
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
    public function findSellCpoItems(array $params): array
    {
        return $this->repository->findSellCpoItems($params);
    }

    public function findSellCpoItemProductID(int $product_id)
    {
        // Input validation
        // ...

        // Fetch data from the database
        $userRow = $this->repository->findSellCpoItemProductID($product_id);

        // Optional: Add or invoke your complex business logic here
        // ...

        // Optional: Map result
        // ...

        return $userRow;
    }

   

    
}
