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
<<<<<<< HEAD
        $customerRow = $this->mapToCustomerRow($data);

        // Insert transferStore
        $id=$this->repository->insertCustomer($customerRow);
=======
        $lotRow = $this->mapToLotRow($data);

        // Insert transferStore
        $id=$this->repository->insertCustomer($lotRow);
>>>>>>> tae

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
<<<<<<< HEAD
    public function updateCustomer(int $customerId, array $data): void
    {
        // Input validation
        $this->validator->validateCustomerUpdate($customerId, $data);

        // Map form data to row
        $storeRow = $this->mapToCustomerRow($data);

        // Insert store
        $this->repository->updateCustomer($customerId, $storeRow);
=======

    public function deleteCustomer(int $lotId, array $data): void
    {

        // Insert store
        $this->repository->deleteCustomer($lotId);
>>>>>>> tae

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }
<<<<<<< HEAD

    public function deleteCustomer(int $customerId, array $data): void
    {
        // Insert store
        $this->repository->deleteCustomer($customerId);
=======
    
    public function updateCustomer(int $lotId, array $data): void
    {
        // Input validation
        $this->validator->validateCustomerUpdate($lotId, $data);

        // Map form data to row
        $storeRow = $this->mapToLotRow($data);

        // Insert store
        $this->repository->updateCustomer($lotId, $storeRow);
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
    private function mapToCustomerRow(array $data): array
=======
    private function mapToLotRow(array $data): array
>>>>>>> tae
    {
        $result = [];

        if (isset($data['customer_name'])) {
            $result['customer_name'] = (string)$data['customer_name'];
        }
        if (isset($data['tel_no'])) {
            $result['tel_no'] = (string)$data['tel_no'];
        }
        if (isset($data['address'])) {
            $result['address'] = (string)$data['address'];
        }


        return $result;
    }
}
