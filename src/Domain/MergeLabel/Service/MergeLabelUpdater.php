<?php

namespace App\Domain\MergeLabel\Service;

use App\Domain\MergeLabel\Repository\MergeLabelRepository;
use App\Domain\Label\Service\LabelFinder;

/**
 * Service.
 */
final class LabelUpdater
{
    private $repository;
    private $validator;
    private $finder;

    public function __construct(
        MergeLabelRepository $repository,
        MergeLabelValidator $validator,
        LabelFinder $finder
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->finder = $finder;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    // public function insertLabel( array $data): int
    // {
    //     // Input validation
    //     $this->validator->validateLabelInsert($data);

    //     // Map form data to row
    //     $lotRow = $this->mapToLabelRow($data);

    //     // Insert transferStore
    //     $id=$this->repository->insertLabel($lotRow);

    //     // Logging
    //     //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
    //     return $id;
    // }
    // public function updateLabel(int $labelId, array $data): void
    // {
    //     // Input validation
    //     $this->validator->validateLabelUpdate($labelId, $data);

    //     // Map form data to row
    //     $storeRow = $this->mapToLabelRow($data);

    //     // Insert store
    //     $this->repository->updateLabel($labelId, $storeRow);
    // }
    
    
    // public function deleteLabel(int $labelId, array $data): void
    // {
    //     $this->repository->deleteLabel($labelId);
    // }

    public function insertMergeLabelApi( array $data,$user_id): int
    {
        // Input validation
        $this->validator->validateMergeLabelInsert($data);

        // Map form data to row
        $row = $this->mapToLabelRow($data);

        // Insert transferStore
        $id=$this->repository->insertMergeLabelApi($row,$user_id);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    public function updateLabelNonfullyApi(int $id, array $data,$user_id): void
    {
        // Input validation
        $this->validator->validateLabelUpdate($id, $data);

        // Map form data to row
        $storeRow = $this->mapToLabelRow($data);

        // Insert store
        $this->repository->updateMergeLabelApi($id, $storeRow,$user_id);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }
    

    // public function deleteLotDefect(int $id): void
    // {

    //     // Insert store
    //     $this->repository->deleteLotDefect($id);

    //     // Logging
    //     //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    // }

    private function mapToLabelRow(array $data): array
    {
        $result = [];

        if (isset($data['lot_id'])) {
            $result['lot_id'] = (string)$data['lot_id'];
        }
        if (isset($data['merge_pack_id'])) {
            $result['merge_pack_id'] = (string)$data['merge_pack_id'];
        }
        if (isset($data['label_no'])) {
            $result['label_no'] = (string)$data['label_no'];
        }
        if (isset($data['label_type'])) {
            $result['label_type'] = (string)$data['label_type'];
        }
        if (isset($data['quantity'])) {
            $result['quantity'] = (string)$data['quantity'];
        }
        if (isset($data['status'])) {
            $result['status'] = (string)$data['status'];
        }


        return $result;
    }
}
