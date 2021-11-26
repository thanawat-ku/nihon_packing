<?php

namespace App\Domain\ScrapDetail\Service;

use App\Domain\ScrapDetail\Repository\ScrapDetailRepository;

/**
 * Service.
 */
final class ScrapDetailFinder
{
    private $repository;

    public function __construct(ScrapDetailRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findScrapDetails(array $params): array
    {
        return $this->repository->findScrapDetails($params);
    }
}
