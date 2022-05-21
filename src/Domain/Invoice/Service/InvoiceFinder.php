<?php

namespace App\Domain\Invoice\Service;

use App\Domain\Invoice\Repository\InvoiceRepository;

/**
 * Service.
 */
final class InvoiceFinder
{
    /**
     * @var InvoiceRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param InvoiceRepository $repository The repository
     */
    public function __construct(InvoiceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find cpo_Items.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findInvoice(array $params): array
    {
        return $this->repository->findInvoice($params);
    }
    public function findInvoicePackings(array $params): array
    {
        return $this->repository->findInvoicePackings($params);
    }
    public function findInvoiceDetails(array $params): array
    {
        return $this->repository->findInvoiceDetails($params);
    }
    public function findInvoiceTagAndLabels(array $params): array
    {
        return $this->repository->findInvoiceTagAndLabels($params);
    }
}
