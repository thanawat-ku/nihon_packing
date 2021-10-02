<?php

namespace App\Domain\CpoItem\Service;

use App\Domain\CpoItem\Repository\CpoItemRepository;

/**
 * Service.
 */
final class CpoItemFinder
{
    /**
     * @var CpoItemRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param CpoItemRepository $repository The repository
     */
    public function __construct(CpoItemRepository $repository)
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
    public function findCpoItem(array $params): array
    {
        return $this->repository->findCpoItem($params);
    }

    public function findCpoItems(array $rtdata): array
    {
        return $this->repository->findCpoItems($rtdata);
    }

    public function findIDFromProductName(array $params, int $ProductID)
    {
        return  $this->repository->findIDFromProductName($params, $ProductID);

        // return $userRow;
    }
}
