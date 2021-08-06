<?php

namespace App\Domain\LotDefect\Service;

use App\Domain\LotDefect\Repository\LotDefectRepository;

/**
 * Service.
 */
final class LotDefectUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        LotDefectRepository $repository,
        LotDefectValidator $validator,
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertLotDefect( array $data): int
    {
        // Input validation
        $this->validator->validateLotDefectInsert($data);

        // Map form data to row
        $lotDefectRow = $this->mapToLotDefectRow($data);

        // Insert transferStore
        $id=$this->repository->insertLotDefect($lotDefectRow);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    public function updateLotDefect(int $id, array $data): void
    {
        // Input validation
        $this->validator->validateLotDefectUpdate($id, $data);

        // Map form data to row
        $storeRow = $this->mapToLotDefectRow($data);

        // Insert store
        $this->repository->updateLotDefect($id, $storeRow);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }
    

    public function deleteLotDefect(int $id): void
    {

        // Insert store
        $this->repository->deleteLotDefect($id);

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
    private function mapToLotDefectRow(array $data): array
    {
        $result = [];

        if (isset($data['lot_id'])) {
            $result['lot_id'] = (string)$data['lot_id'];
        }
        if (isset($data['defect_id'])) {
            $result['defect_id'] = (string)$data['defect_id'];
        }
        if (isset($data['quantity'])) {
            $result['quantity'] = (string)$data['quantity'];
        }

        return $result;
    }
}
