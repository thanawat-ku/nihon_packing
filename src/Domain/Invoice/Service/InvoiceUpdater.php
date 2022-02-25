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
}
