<?php

namespace App\Domain\CheckLabel\Service;

use App\Domain\CheckLabel\Repository\CheckLabelRepository;

/**
 * Service.
 */
final class CheckLabelFinder
{
    private $repository;
    public function __construct(CheckLabelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findCheckLabels(array $params): array
    {
        return $this->repository->findCheckLabels($params);
    }
}
