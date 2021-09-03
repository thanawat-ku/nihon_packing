<?php

namespace App\Domain\CreateMergeNoFromLabel\Service;

use App\Domain\CreateMergeNoFromLabel\Repository\CreateMergeNoFromLabelRepository;
use App\Domain\CreateMergeNoFromLabel\Service\CreateMergeNoFromLabelFinder;

/**
 * Service.
 */
final class CreateMergeNoFromLabelUpdater
{
    private $repository;
    private $validator;
    private $finder;

    public function __construct(
        CreateMergeNoFromLabelRepository $repository,
        CreateMergeNoFromLabelValidator $validator,
        CreateMergeNoFromLabelFinder $finder
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->finder = $finder;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }
    
    
    public function deleteCreateMergeNoFromLabel(int $CreateMergeNoFromLabelId, array $data): void
    {
        $this->repository->deleteCreateMergeNoFromLabel($CreateMergeNoFromLabelId);
    }

    private function mapToCreateMergeNoFromLabelRow(array $data): array
    {
        $result = [];

        if (isset($data['lot_id'])) {
            $result['lot_id'] = (string)$data['lot_id'];
        }
        if (isset($data['merge_pack_id'])) {
            $result['merge_pack_id'] = (string)$data['merge_pack_id'];
        }
        if (isset($data['CreateMergeNoFromLabel_no'])) {
            $result['CreateMergeNoFromLabel_no'] = (string)$data['CreateMergeNoFromLabel_no'];
        }
        if (isset($data['CreateMergeNoFromLabel_type'])) {
            $result['CreateMergeNoFromLabel_type'] = (string)$data['CreateMergeNoFromLabel_type'];
        }
        if (isset($data['quantity'])) {
            $result['quantity'] = (string)$data['quantity'];
        }
        if (isset($data['status'])) {
            $result['status'] = (string)$data['status'];
        }


        return $result;
    }
    private function mapToMergePackRow(array $data): array
    {
        $result = [];

        if (isset($data['product_id'])) {
            $result['product_id'] = (string)$data['product_id'];
        }
        if (isset($data['merge_no'])) {
            $result['merge_no'] = (string)$data['merge_no'];
        }
        if (isset($data['merge_status'])) {
            $result['merge_status'] = (string)$data['merge_status'];
        }

        return $result;
    }

    public function insertCreateMergeNoFromLabelMergePackApi( array $data, $user_id): int
    {
        // Input validation
        $this->validator->validateCreateMergeNoFromLabelInsert($data);

        // Map form data to row
        $lotRow = $this->mapToCreateMergeNoFromLabelRow($data);

        // Insert transferStore
        $id=$this->repository->insertCreateMergeNoFromLabelMergePackApi($lotRow, $user_id);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }

    public function updateCreateMergeNoFromLabelMergePackApi(int $CreateMergeNoFromLabelId, array $data,$user_id): void
    {
        // Input validation
        $this->validator->validateCreateMergeNoFromLabelUpdate($CreateMergeNoFromLabelId, $data);

        // Map form data to row
        $storeRow = $this->mapToCreateMergeNoFromLabelRow($data);

        // Insert store
        $this->repository->updateCreateMergeNoFromLabelMergePackApi($CreateMergeNoFromLabelId, $storeRow,$user_id);
    }

    public function insertMergePackApi( array $data, $user_id): int
    {
        // Input validation
        $this->validator->validateCreateMergeNoFromLabelInsert($data);

        // Map form data to row
        $lotRow = $this->mapToMergePackRow($data);

        // Insert transferStore
        $id=$this->repository->insertMergePackApi($lotRow, $user_id);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }

}
