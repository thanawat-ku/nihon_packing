<?php

namespace App\Domain\LabelPackMerge\Service;

use App\Domain\LabelPackMerge\Repository\LabelPackMergeRepository;
use App\Domain\LabelPackMerge\Service\LabelPackMergeFinder;

/**
 * Service.
 */
final class LabelPackMergeUpdater
{
    private $repository;
    private $validator;
    private $finder;

    public function __construct(
        LabelPackMergeRepository $repository,
        LabelPackMergeValidator $validator,
        LabelPackMergeFinder $finder
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->finder = $finder;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertLabelPackMergeApi( array $data): int
    {
        // Input validation
        $this->validator->validateLabelPackMergeInsert($data);

        // Map form data to row
        $lotRow = $this->mapToLabelPackMergeRow($data);

        // Insert transferStore
        $id=$this->repository->insertLabelPackMerge($lotRow);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    public function updateLabelPackMergeApi(string $labelId, array $data,$user_id): void
    {
       
        // Input validation
        $this->validator->validateLabelPackMergeUpdate($labelId, $data);

        // Map form data to row
        $storeRow = $this->mapToLabelPackMergeRow($data);

        // Insert store
        $this->repository->updateLabelPackMergeApi($labelId, $storeRow, $user_id);
    }

    public function deleteLabelMergePackApi(int $labelId, array $data): void
    {
        $this->repository->deleteLabelMergePackApi($labelId);
    }
    
    
    public function deleteLabelPackMergeApi(int $labelId, array $data): void
    {
        $this->repository->deleteLabelPackMergeApi($labelId);
    }

    private function mapToLabelPackMergeRow(array $data): array
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
