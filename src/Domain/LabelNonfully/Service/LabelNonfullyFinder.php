<?php

namespace App\Domain\LabelNonfully\Service;

use App\Domain\LabelNonfully\Repository\LabelNonfullyRepository;

/**
 * Service.
 */
final class LabelNonfullyFinder
{
    private $repository;
    public function __construct(LabelNonfullyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findLabelNonfullys(array $params): array
    {
        return $this->repository->findLabelNonfullys($params);
    }
}
