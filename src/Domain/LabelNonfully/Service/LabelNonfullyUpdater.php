<?php

namespace App\Domain\LabelNonfully\Service;

use App\Domain\LabelNonfully\Repository\LabelNonfullyRepository;
use App\Domain\LabelNonfully\Service\LabelNonfullyFinder;

/**
 * Service.
 */
final class LabelNonfullyUpdater
{
    private $repository;
    private $validator;
    private $finder;

    public function __construct(
        LabelNonfullyRepository $repository,
        LabelNonfullyValidator $validator,
        LabelNonfullyFinder $finder
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->finder = $finder;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    // public function insertLabelNonfullyApi( array $data): int
    // {
    //     // Input validation
    //     $this->validator->validateLabelNonfullyInsert($data);

    //     // Map form data to row
    //     $lotRow = $this->mapToLabelNonfullyRow($data);

    //     // Insert transferStore
    //     $id=$this->repository->insertLabelNonfully($lotRow);

    //     // Logging
    //     //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
    //     return $id;
    // }
    public function updateLabelNonfullyApi(int $labelId, array $data,$user_id): void
    {
        // Input validation
        $this->validator->validateLabelNonfullyUpdateApi($labelId, $data);

        // Map form data to row
        $storeRow = $this->mapToLabelNonfullyRow($data);

        // Insert store
        $this->repository->updateLabelNonfullyApi($labelId, $storeRow,$user_id);
    }
    
    
    // public function deleteLabelNonfullyApi(int $labelId, array $data): void
    // {
    //     $this->repository->deleteLabelNonfully($labelId);
    // }

    private function mapToLabelNonfullyRow(array $data): array
    {
        $result = [];

        // if (isset($data['merge_pack_id'])) {
        //     $result['merge_pack_id'] = (string)$data['merge_pack_id'];
        // }
        if (isset($data['label_type'])) {
            $result['label_type'] = (string)$data['label_type'];
        }
        if (isset($data['status'])) {
            $result['status'] = (string)$data['status'];
        }
        // if (isset($data['label_type'])) {
        //     $result['label_type'] = (string)$data['label_type'];
        // }
        // if (isset($data['quantity'])) {
        //     $result['quantity'] = (string)$data['quantity'];
        // }
        // if (isset($data['status'])) {
        //     $result['status'] = (string)$data['status'];
        // }


        return $result;
    }
}
