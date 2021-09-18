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
        $productRow = $this->mapToProductRow($data);

        // Insert transferStore
        $id=$this->repository->insertProduct($productRow);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    public function updateProduct(int $productId, array $data): void
    {
        // Input validation
        $this->validator->validateProductUpdate($productId, $data);

        // Map form data to row
        $storeRow = $this->mapToProductRow($data);

        // Insert store
        $this->repository->updateProduct($productId, $storeRow);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }
    public function deleteProduct(int $productId, array $data): void
    {

        // Insert store
        $this->repository->deleteProduct($productId);

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
    private function mapToProductRow(array $data): array
    {
        $result = [];

        if (isset($data['part_code'])) {
            $result['part_code'] = (string)$data['part_code'];
        }
        if (isset($data['part_name'])) {
            $result['part_name'] = (string)$data['part_name'];
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
