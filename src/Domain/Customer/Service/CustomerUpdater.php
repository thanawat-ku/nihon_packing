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
        $lotRow = $this->mapToLotRow($data);

        // Insert transferStore
        $id=$this->repository->insertCustomer($lotRow);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    
    public function updateCustomer(int $lotId, array $data): void
    {
        // Input validation
        $this->validator->validateCustomerUpdate($lotId, $data);

        // Map form data to row
        $storeRow = $this->mapToLotRow($data);

        // Insert store
        $this->repository->updateCustomer($lotId, $storeRow);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

    private function mapToLotRow(array $data): array
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
