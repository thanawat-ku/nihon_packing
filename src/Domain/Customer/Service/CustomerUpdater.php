<?php

namespace App\Domain\Customer\Service;

use App\Domain\Customer\Repository\CustomerRepository;

/**
 * Service.
 */
final class CustomerUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        CustomerRepository $repository,
        CustomerValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertCustomer( array $data): int
    {
        // Input validation
        $this->validator->validateCustomerInsert($data);

        // Map form data to row
        $customerRow = $this->mapToCustomerRow($data);

        // Insert transferStore
        $id=$this->repository->insertCustomer($customerRow);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    public function updateCustomer(int $customerId, array $data): void
    {
        // Input validation
        $this->validator->validateCustomerUpdate($customerId, $data);

        // Map form data to row
        $storeRow = $this->mapToCustomerRow($data);

        // Insert store
        $this->repository->updateCustomer($customerId, $storeRow);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

    public function deleteCustomer(int $customerId, array $data): void
    {
        // Insert store
        $this->repository->deleteCustomer($customerId);

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
    private function mapToCustomerRow(array $data): array
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
