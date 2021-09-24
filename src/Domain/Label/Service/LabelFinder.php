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
    public function findLabelNonfullys(array $params): array
    {
        return $this->repository->findLabelNonfullys($params);
    }

    // public function checklabel(string $labelNO)
    // {
    //     $labelRow = $this->repository->checklabel($labelNOO);
    //     return $labelRow;
    // }

    public function checklabel(string $labelNO)
    {
        // Input validation
        // ...

        // Fetch data from the database
        $userRow = $this->repository->checklabel($labelNO);

        // Optional: Add or invoke your complex business logic here
        // ...

        // Optional: Map result
        // ...

        return $userRow;
    }
}

final class LabelPackMergeFinder
{
    private $repository;
    public function __construct(LabelRepository $repository)
    {
        $this->repository = $repository;
    }

    // public function findLabelPackMerges(array $params): array
    // {
    //     return $this->repository->findLabelPackMerges($params);
    // }
}
