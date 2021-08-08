<?php

namespace App\Domain\MergePack\Service;

use App\Domain\MergePack\Repository\MergePackRepository;

/**
 * Service.
 */
final class MergePackUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        MergePackRepository $repository,
        MergePackValidator $validator,
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertMergePackApi( array $data,$user_id): int
    {
        // Input validation
        $this->validator->validateMergePackInsert($data);

        // Map form data to row
        $row = $this->mapToMergePackRow($data);

        // Insert transferStore
        $id=$this->repository->insertMergePackApi($row,$user_id);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    public function updateMergePackApi(int $id, array $data,$user_id): void
    {
        // Input validation
        $this->validator->validateMergePackUpdate($id, $data);

        // Map form data to row
        $storeRow = $this->mapToMergePackRow($data);

        // Insert store
        $this->repository->updateMergePackApi($id, $storeRow,$user_id);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }
    

    public function deleteMergePack(int $id): void
    {

        // Insert store
        $this->repository->deleteMergePack($id);

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
    private function mapToMergePackRow(array $data): array
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
