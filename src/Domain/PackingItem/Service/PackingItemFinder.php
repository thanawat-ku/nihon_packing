<?php

namespace App\Domain\PackingItem\Service;

use App\Domain\PackingItem\Repository\PackingItemRepository;

/**
 * Service.
 */
final class PackingItemFinder
{
    /**
     * @var PackingItemRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param PackingItemRepository $repository The repository
     */
    public function __construct(PackingItemRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find cpo_Items.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findPackingItem(array $params): array
    {
        return $this->repository->findPackingItem($params);
    }

    public function findPackingItemSelect(array $rtdata): array
    {
        return $this->repository->findPackingItemSelect($rtdata);
    }

    public function findIDFromProductName(array $params, int $ProductID)
    {
        return  $this->repository->findIDFromProductName($params, $ProductID);

        // return $userRow;
    }
}
