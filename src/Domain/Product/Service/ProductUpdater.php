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
<<<<<<< HEAD
        $productRow = $this->mapToProductRow($data);

        // Insert transferStore
        $id=$this->repository->insertProduct($productRow);
=======
        $lotRow = $this->mapToLotRow($data);

        // Insert transferStore
        $id=$this->repository->insertProduct($lotRow);
>>>>>>> tae

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
<<<<<<< HEAD
    public function updateProduct(int $productId, array $data): void
    {
        // Input validation
        $this->validator->validateProductUpdate($productId, $data);

        // Map form data to row
        $storeRow = $this->mapToProductRow($data);

        // Insert store
        $this->repository->updateProduct($productId, $storeRow);
=======
    
    public function updateProduct(int $lotId, array $data): void
    {
        // Input validation
        $this->validator->validateProductUpdate($lotId, $data);

        // Map form data to row
        $storeRow = $this->mapToLotRow($data);

        // Insert store
        $this->repository->updateProduct($lotId, $storeRow);
>>>>>>> tae

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }
<<<<<<< HEAD
    public function deleteProduct(int $productId, array $data): void
    {

        // Insert store
        $this->repository->deleteProduct($productId);
=======

    public function deleteProduct(int $lotId, array $data): void
    {

        // Insert store
        $this->repository->deleteProduct($lotId);
>>>>>>> tae

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

<<<<<<< HEAD
    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToProductRow(array $data): array
=======
    private function mapToLotRow(array $data): array
>>>>>>> tae
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

<<<<<<< HEAD
=======

>>>>>>> tae
        return $result;
    }
}
