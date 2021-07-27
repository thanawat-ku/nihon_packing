<?php

namespace App\Domain\Product\Service;

use App\Domain\Product\Repository\ProductRepository;

/**
 * Service.
 */
final class ProductUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        ProductRepository $repository,
        ProductValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertProduct( array $data): int
    {
        // Input validation
        $this->validator->validateProductInsert($data);

        // Map form data to row
        $lotRow = $this->mapToLotRow($data);

        // Insert transferStore
        $id=$this->repository->insertProduct($lotRow);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    
    public function updateProduct(int $lotId, array $data): void
    {
        // Input validation
        $this->validator->validateProductUpdate($lotId, $data);

        // Map form data to row
        $storeRow = $this->mapToLotRow($data);

        // Insert store
        $this->repository->updateProduct($lotId, $storeRow);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

    private function mapToLotRow(array $data): array
    {
        $result = [];

        if (isset($data['product_code'])) {
            $result['product_code'] = (string)$data['product_code'];
        }
        if (isset($data['product_name'])) {
            $result['product_name'] = (string)$data['product_name'];
        }
        if (isset($data['price'])) {
            $result['price'] = (string)$data['price'];
        }
        if (isset($data['std_pack'])) {
            $result['std_pack'] = (string)$data['std_pack'];
        }
        if (isset($data['std_box'])) {
            $result['std_box'] = (string)$data['std_box'];
        }


        return $result;
    }
}
