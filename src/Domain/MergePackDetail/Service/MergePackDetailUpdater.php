<?php

namespace App\Domain\MergePackDetail\Service;

use App\Domain\MergePackDetail\Repository\MergePackDetailRepository;
use App\Domain\MergePackDetail\Service\MergePackDetailFinder;

/**
 * Service.
 */
final class MergePackDetailUpdater
{
    private $repository;
    private $validator;
    private $finder;

    public function __construct(
        MergePackDetailRepository $repository,
        MergePackDetailValidator $validator,
        MergePackDetailFinder $finder
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->finder = $finder;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertMergePackDetailApi( array $data, $user_id): int
    {
        // Input validation
        $this->validator->validateMergePackDetailInsert($data);

        // Map form data to row
        $lotRow = $this->mapToMergePackDetailRow($data);

        // Insert transferStore
        $id=$this->repository->insertMergePackDetailApi($lotRow, $user_id);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    public function updateMergePackDetailApi(int $labelId, array $data, $user_id): void
    {
        // Input validation
        $this->validator->validateMergePackDetailUpdate($labelId, $data);

        // Map form data to row
        $storeRow = $this->mapToMergePackDetailRow($data);

        // Insert store
        $this->repository->updateMergePackDetailApi($labelId, $storeRow, $user_id);
    }

    public function updateLabelMergePackApi(int $labelId, array $data, $user_id): void
    {
        // Input validation
        $this->validator->validateMergePackDetailUpdate($labelId, $data);

        // Map form data to row
        $storeRow = $this->mapToLabelRow($data);

        // Insert store
        $this->repository->updateLabelApi($labelId, $storeRow, $user_id);
    }
    
    
    public function deleteLabelMergePackApi(int $labelId, array $data): void
    {
        $this->repository->deleteLabelMergePackApi($labelId);
    }

    

    private function mapToMergePackDetailRow(array $data): array
    {
        $result = [];

        if (isset($data['merge_pack_id'])) {
            $result['merge_pack_id'] = (string)$data['merge_pack_id'];
        }
        if (isset($data['label_id'])) {
            $result['label_id'] = (string)$data['label_id'];
        }
        


        return $result;
    }

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

    // private function mapToMergePackDetailRow(array $data): array
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
}
