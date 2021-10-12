<?php

namespace App\Domain\LabelVoidReason\Service;

use App\Domain\LabelVoidReason\Repository\LabelVoidReasonRepository;

/**
 * Service.
 */
final class LabelVoidReasonFinder
{
    private $repository;

    public function __construct(LabelVoidReasonRepository $repository)
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
    public function findLabelVoidReasons(array $params): array
    {
        return $this->repository->findLabelVoidReasons($params);
    }
}
