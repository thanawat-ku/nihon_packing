<?php

namespace App\Domain\LabelVoidReason\Service;

use App\Domain\LabelVoidReason\Repository\LabelVoidReasonRepository;

/**
 * Service.
 */
final class LabelVoidReasonUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        LabelVoidReasonRepository $repository,
        LabelVoidReasonValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertLabelVoidReason( array $data): int
    {
        // Input validation
        $this->validator->validateLabelVoidReasonInsert($data);

        // Map form data to row
        $customerRow = $this->mapToLabelVoidReasonRow($data);

        // Insert transferStore
        $id=$this->repository->insertLabelVoidReason($customerRow);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    public function updateLabelVoidReason(int $customerId, array $data): void
    {
        // Input validation
        $this->validator->validateLabelVoidReasonUpdate($customerId, $data);

        // Map form data to row
        $storeRow = $this->mapToLabelVoidReasonRow($data);

        // Insert store
        $this->repository->updateLabelVoidReason($customerId, $storeRow);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

    public function deleteLabelVoidReason(int $customerId, array $data): void
    {
        // Insert store
        $this->repository->deleteLabelVoidReason($customerId);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToLabelVoidReasonRow(array $data): array
    {
        $result = [];

        if (isset($data['customer_name'])) {
            $result['customer_name'] = (string)$data['customer_name'];
        }
        if (isset($data['customer_code'])) {
            $result['customer_code'] = (string)$data['customer_code'];
        }

        return $result;
    }
}
