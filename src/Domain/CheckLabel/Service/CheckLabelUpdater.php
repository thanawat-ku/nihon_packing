<?php

namespace App\Domain\CheckLabel\Service;

use App\Domain\CheckLabel\Repository\CheckLabelRepository;
use App\Domain\CheckLabel\Service\CheckLabelFinder;

/**
 * Service.
 */
final class CheckLabelUpdater
{
    private $repository;
    private $validator;
    private $finder;

    public function __construct(
        CheckLabelRepository $repository,
        CheckLabelValidator $validator,
        CheckLabelFinder $finder
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->finder = $finder;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    // public function insertCheckLabel( array $data): int
    // {
    //     // Input validation
    //     $this->validator->validateCheckLabelInsert($data);

    //     // Map form data to row
    //     $lotRow = $this->mapToCheckLabelRow($data);

    //     // Insert transferStore
    //     $id=$this->repository->insertCheckLabel($lotRow);

    //     // Logging
    //     //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
    //     return $id;
    // }
    // public function updateCheckLabel(int $labelId, array $data): void
    // {
    //     // Input validation
    //     $this->validator->validateCheckLabelUpdate($labelId, $data);

    //     // Map form data to row
    //     $storeRow = $this->mapToCheckLabelRow($data);

    //     // Insert store
    //     $this->repository->updateCheckLabel($labelId, $storeRow);
    // }
    
    
    // public function deleteCheckLabel(int $labelId, array $data): void
    // {
    //     $this->repository->deleteCheckLabel($labelId);
    // }

    // private function mapToCheckLabelRow(array $data): array
    // {
    //     $result = [];

    //     if (isset($data['lot_id'])) {
    //         $result['lot_id'] = (string)$data['lot_id'];
    //     }
    //     if (isset($data['merge_pack_id'])) {
    //         $result['merge_pack_id'] = (string)$data['merge_pack_id'];
    //     }
    //     if (isset($data['label_no'])) {
    //         $result['label_no'] = (string)$data['label_no'];
    //     }
    //     if (isset($data['label_type'])) {
    //         $result['label_type'] = (string)$data['label_type'];
    //     }
    //     if (isset($data['quantity'])) {
    //         $result['quantity'] = (string)$data['quantity'];
    //     }
    //     if (isset($data['status'])) {
    //         $result['status'] = (string)$data['status'];
    //     }


    //     return $result;
    // }

    // public function insertCheckLabelMergePackApi( array $data, $user_id): int
    // {
    //     // Input validation
    //     $this->validator->validateCheckLabelInsert($data);

    //     // Map form data to row
    //     $lotRow = $this->mapToCheckLabelRow($data);

    //     // Insert transferStore
    //     $id=$this->repository->insertCheckLabelMergePackApi($lotRow, $user_id);

    //     // Logging
    //     //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
    //     return $id;
    // }

    // public function updateCheckLabelMergePackApi(int $labelId, array $data,$user_id): void
    // {
    //     // Input validation
    //     $this->validator->validateCheckLabelUpdate($labelId, $data);

    //     // Map form data to row
    //     $storeRow = $this->mapToCheckLabelRow($data);

    //     // Insert store
    //     $this->repository->updateCheckLabelMergePackApi($labelId, $storeRow,$user_id);
    // }
}
