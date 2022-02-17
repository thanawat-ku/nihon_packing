<?php

namespace App\Domain\Printer\Service;

use App\Domain\Printer\Repository\PrinterRepository;

/**
 * Service.
 */
final class PrinterFinder
{
    private $repository;

    public function __construct(PrinterRepository $repository)
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
    public function findPrinters(array $params): array
    {
        return $this->repository->findPrinters($params);
    }
}
