<?php

namespace App\Domain\Sell\Service;

use App\Domain\Sell\Repository\SellRepository;

/**
 * Service.
 */
final class SellUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        SellRepository $repository,
        SellValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertSellApi(array $data, $user_id): int
    {
        // Input validation
        $this->validator->validateSellInsert($data);

        // Map form data to row
        $productRow = $this->mapToRow($data);

        // Insert transferStore
        $id=$this->repository->insertSellApi($productRow, $user_id);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    public function updateSellApi(int $sellId, array $data): void
    {
        // Input validation
        $this->validator->validateSellUpdate($sellId, $data);

        // Map form data to row
        $storeRow = $this->mapToRow($data);

        // Insert store
        $this->repository->updateSellApi($sellId, $storeRow);
    }
    public function deleteSellApi(int $productId, array $data): void
    {

        // Insert store
        $this->repository->deleteSell($productId);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

    
    private function mapToRow(array $data): array
    {
        $result = [];

        if (isset($data['sell_no'])) {
            $result['sell_no'] = (string)$data['sell_no'];
        }
        if (isset($data['sell_date'])) {
            $result['sell_date'] = (string)$data['sell_date'];
        }
        if (isset($data['product_id'])) {
            $result['product_id'] = (int)$data['product_id'];
        }
        if (isset($data['total_qty'])) {
            $result['total_qty'] = (int)$data['total_qty'];
        }
        if (isset($data['sell_status'])) {
            $result['sell_status'] = (string)$data['sell_status'];
        }

        return $result;
    }
}
