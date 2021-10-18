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
    public function updateLabelVoidReason(int $voidReasonId, array $data): void
    {
        // Input validation
        $this->validator->validateLabelVoidReasonUpdate($voidReasonId, $data);

        // Map form data to row
        $storeRow = $this->mapToLabelVoidReasonRow($data);

        // Insert store
        $this->repository->updateLabelVoidReason($voidReasonId, $storeRow);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

    public function deleteLabelVoidReason(int $voidReasonId): void
    {
        // Insert store
        $this->repository->deleteLabelVoidReason($voidReasonId);

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

        if (isset($data['reason_name'])) {
            $result['reason_name'] = (string)$data['reason_name'];
        }
        if (isset($data['description'])) {
            $result['description'] = (string)$data['description'];
        }

        return $result;
    }
}
