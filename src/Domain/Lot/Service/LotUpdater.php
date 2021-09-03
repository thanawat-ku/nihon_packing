<?php

namespace App\Domain\Lot\Service;

use App\Domain\Lot\Repository\LotRepository;

/**
 * Service.
 */
final class LotUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        LotRepository $repository,
        LotValidator $validator,
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertLot( array $data): int
    {
        // Input validation
        $this->validator->validateLotInsert($data);

        // Map form data to row
        $lotRow = $this->mapToLotRow($data);

        // Insert transferStore
        $id=$this->repository->insertLot($lotRow);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    public function updateLotApi(int $lotId, array $data, $user_id): void
    {
        // Input validation
        $this->validator->validateLotUpdate($lotId, $data);

        // Map form data to row
        $storeRow = $this->mapToLotRow($data);

        // Insert store
        $this->repository->updateLotApi($lotId, $storeRow,$user_id);
    }

    public function updateLot(int $lotId, array $data): void
    {
        // Input validation
        $this->validator->validateLotUpdate($lotId, $data);

        // Map form data to row
        $storeRow = $this->mapToLotRow($data);

        // Insert store
        $this->repository->updateLot($lotId, $storeRow);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }
    
    public function printLot(int $lotId): void
    {
        $this->repository->printLot($lotId);
    }

    public function deleteLot(int $lotId, array $data): void
    {

        // Insert store
        $this->repository->deleteLot($lotId);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

    private function mapToLotRow(array $data): array
    {
        $result = [];

        if (isset($data['lot_no'])) {
            $result['lot_no'] = (string)$data['lot_no'];
        }
        if (isset($data['product_id'])) {
            $result['product_id'] = (string)$data['product_id'];
        }
        if (isset($data['quantity'])) {
            $result['quantity'] = (string)$data['quantity'];
        }
        if (isset($data['status'])) {
            $result['status'] = (string)$data['status'];
        }

        return $result;
    }
}
