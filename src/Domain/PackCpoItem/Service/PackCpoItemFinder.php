<?php

namespace App\Domain\PackCpoItem\Service;

use App\Domain\PackCpoItem\Repository\PackCpoItemRepository;

/**
 * Service.
 */
final class PackCpoItemFinder
{
    /**
     * @var PackCpoItemRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param PackCpoItemRepository $repository The repository
     */
    public function __construct(PackCpoItemRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find packs.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findPackCpoItems(array $params): array
    {
        return $this->repository->findPackCpoItems($params);
    }

    public function findPackCpoItemProductID(int $product_id)
    {
        // Input validation
        // ...

        // Fetch data from the database
        $userRow = $this->repository->findPackCpoItemProductID($product_id);

        // Optional: Add or invoke your complex business logic here
        // ...

        // Optional: Map result
        // ...

        return $userRow;
    }

   

    
}
