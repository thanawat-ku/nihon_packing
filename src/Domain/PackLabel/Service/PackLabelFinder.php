<?php

namespace App\Domain\PackLabel\Service;

use App\Domain\PackLabel\Repository\PackLabelRepository;

/**
 * Service.
 */
final class PackLabelFinder
{
    /**
     * @var PackLabelRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param PackLabelRepository $repository The repository
     */
    public function __construct(PackLabelRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find pack_labels.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findPackLabels(array $params): array
    {
        return $this->repository->findPackLabels($params);
    }

    public function findPackLabelForLots(array $params): array
    {
        return $this->repository->findPackLabelForLots($params);
    }
    public function findPackLabelForMergePacks(array $params): array
    {
        return $this->repository->findPackLabelForMergePacks($params);
    }
    public function checkLabelInPackLabel(string $labelNo)
    {
        $row = $this->repository->checkLabelInPackLabel($labelNo);

        return $row;
    }
}
