<?php

namespace App\Domain\Scrap\Service;

use App\Domain\Scrap\Repository\ScrapRepository;

/**
 * Service.
 */
final class ScrapFinder
{
    private $repository;

    public function __construct(ScrapRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find customers.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findScraps(array $params): array
    {
        return $this->repository->findScraps($params);
    }
}
