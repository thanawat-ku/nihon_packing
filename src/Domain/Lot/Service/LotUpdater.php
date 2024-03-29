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
        LotValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function insertLot(array $data): int
    {
        // Input validation
        $this->validator->validateLotInsert($data);

        // Map form data to row
        $lotRow = $this->mapToLotRow($data);

        // Insert transferStore
        $id = $this->repository->insertLot($lotRow);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    public function registerLotApi(int $lotId, array $data, $user_id): void
    {
        // Input validation
        $this->validator->validateLotUpdate($lotId, $data);

        // Map form data to row
        $storeRow = $this->mapToLotRow($data);

        // Insert store
        $this->repository->registerLotApi($lotId, $storeRow, $user_id);
    }

    public function confirmLotApi(int $lotId, array $data, $user_id): void
    {
        // Input validation
        $this->validator->validateLotUpdate($lotId, $data);

        // Map form data to row
        $storeRow = $this->mapToLotRow($data);

        // Insert store
        $this->repository->confirmLotApi($lotId, $storeRow, $user_id);
    }

    public function updateLotApi(int $lotId, array $data, $user_id): void
    {
        // Input validation
        $this->validator->validateLotUpdate($lotId, $data);

        // Map form data to row
        $storeRow = $this->mapToLotRow($data);

        // Insert store
        $this->repository->updateLotApi($lotId, $storeRow, $user_id);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
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

    public function updateLotNsp(int $lotId, array $data): void
    {
        // Input validation
        $this->validator->validateLotUpdate($lotId, $data);

        $row = $this->mapToLotRowNsp($data);

        // Insert store
        $this->repository->updateLotNsp($lotId, $row);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

    public function registerLot(int $lotId, array $data): void
    {
        // Input validation
        $this->validator->validateLotUpdate($lotId, $data);

        // Map form data to row
        $storeRow = $this->mapToLotRow($data);

        // Insert store
        $this->repository->registerLot($lotId, $storeRow);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

    public function printLot(int $lotId, array $data): void
    {
        $this->repository->printLot($lotId, $data);
    }

    public function deleteLot(int $lotId, array $data): void
    {

        // Insert store
        $this->repository->deleteLot($lotId);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

    public function IsdeleteLot(int $lotId, array $data): void
    {

        $IsDelete['is_delete'] = "Y";
        $this->repository->updateLot($lotId, $IsDelete);
    }

    private function mapToLotRow(array $data): array
    {
        $result = [];

        if (isset($data['id'])) {
            $result['id'] = $data['id'];
        }
        if (isset($data['lot_no'])) {
            $result['lot_no'] = (string)$data['lot_no'];
        }
        if (isset($data['generate_lot_no'])) {
            $result['generate_lot_no'] = (string)$data['generate_lot_no'];
        }
        if (isset($data['product_id'])) {
            $result['product_id'] = (string)$data['product_id'];
        }
        if (isset($data['quantity'])) {
            $result['quantity'] = (string)$data['quantity'];
        }
        if (isset($data['real_qty'])) {
            $result['real_qty'] = (string)$data['real_qty'];
        }
        if (isset($data['real_lot_qty'])) {
            $result['real_lot_qty'] = (string)$data['real_lot_qty'];
        }
        if (isset($data['printed_user_id'])) {
            $result['printed_user_id'] = (string)$data['printed_user_id'];
        }
        if (isset($data['packed_user_id'])) {
            $result['packed_user_id'] = (string)$data['packed_user_id'];
        }
        if (isset($data['issue_date'])) {
            $result['issue_date'] = (string)$data['issue_date'];
        }
        if (isset($data['status'])) {
            $result['status'] = (string)$data['status'];
        }
        if (isset($data['is_delete'])) {
            $result['is_delete'] = (string)$data['is_delete'];
        }
        if (isset($data['stock_control_id'])) {
            $result['stock_control_id'] = (string)$data['stock_control_id'];
        }
        if (isset($data['PackingQty'])) {
            $result['PackingQty'] = (string)$data['PackingQty'];
        }


        return $result;
    }

    private function mapToLotRowNsp(array $data): array
    {
        $result = [];

        if (isset($data['real_qty'])) {
            $result['CurrentQty'] = (string)$data['real_qty'];
            $result['EndQty'] = (string)$data['real_qty'];
        }
        if (isset($data['PackingQty'])) {
            $result['PackingQty'] = (string)$data['PackingQty'];
        }

        return $result;
    }
}
