<?php

namespace App\Domain\Section\Service;

use App\Domain\Section\Repository\SectionRepository;

/**
 * Service.
 */
final class SectionFinder
{
    private $repository;

    public function __construct(SectionRepository $repository)
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
    public function findSections(array $params): array
    {
        return $this->repository->findSections($params);
    }
}
