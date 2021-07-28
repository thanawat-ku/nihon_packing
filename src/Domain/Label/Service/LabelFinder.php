<?php

namespace App\Domain\Label\Service;

use App\Domain\Label\Repository\LabelRepository;

/**
 * Service.
 */
final class LabelFinder
{
    private $repository;
    public function __construct(LabelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findLabels(array $params): array
    {
        return $this->repository->findLabels($params);
    }
}
