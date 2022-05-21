<?php

namespace App\Domain\Invoice\Service;

use App\Domain\Invoice\Repository\InvoiceRepository;

use function DI\string;

/**
 * Service.
 */
final class InvoiceUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        InvoiceRepository $repository,
        InvoiceValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function updateInvoice(int $id, array $data): void
    {
        $this->validator->validateInvoiceUpdate($id, $data);

        $row = $this->mapToRow($data);

        $this->repository->updateInvoice($id, $row);
    }

    public function updateInvoicePacking(int $id, array $data, $user_id): void
    {
        $this->validator->validateInvoiceUpdate($id, $data);

        $row = $this->mapToRowPacking($data);

        $this->repository->updateInvoicePacking($id, $row, $user_id);
    }

    public function  insertInvoicePacking(array $data, $user_id): int
    {
        $this->validator->validateInvoiceInsert($data);

        $row = $this->mapToRowPacking($data);

        $id = $this->repository->insertInvoicePacking($row, $user_id);

        return $id;
    }

    public function deleteInvoice(int $id): void
    {
        $this->repository->deleteInvoice($id);
    }

    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToRow(array $data): array
    {
        $result = [];

        if (isset($data['PackingQty'])) {
            $result['PackingQty'] = $data['PackingQty'];
        }

        return $result;
    }

    private function mapToRowPacking(array $data): array
    {
        $result = [];

        if (isset($data['invoice_no'])) {
            $result['invoice_no'] = $data['invoice_no'];
        }
        if (isset($data['customer_id'])) {
            $result['customer_id'] = $data['customer_id'];
        }
        if (isset($data['date'])) {
            $result['date'] = $data['date'];
        }
        if (isset($data['invoice_status'])) {
            $result['invoice_status'] = $data['invoice_status'];
        }

        return $result;
    }
}
