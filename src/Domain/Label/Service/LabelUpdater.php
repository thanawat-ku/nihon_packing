<?php

namespace App\Domain\Label\Service;

use App\Domain\Label\Repository\LabelRepository;
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
        LabelRepository $repository,
        LabelValidator $validator,
        LabelFinder $finder
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->finder = $finder;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertLabel( array $data): int
    {

        $this->validator->validateLabelInsert($data);

        $lotRow = $this->mapToLabelRow($data);

        $id=$this->repository->insertLabel($lotRow);

        
        return $id;
    }

    public function insertLabelApi( array $data,$user_id): int //สร้าง labels จาก splitlabel
    {

        $this->validator->validateLabelInsert($data);

        $Row = $this->mapToLabelRow($data);

        $id=$this->repository->insertLabelApi($Row,$user_id);

        
        return $id;
    }

    public function updateLabelApi(int $labelID, array $data ,$user_id): void
    {
        // Input validation
        $this->validator->validateLabelUpdate($labelID, $data);

        // Map form data to row
        $storeRow = $this->mapToLabelRow($data);

        // Insert store
        $this->repository->updateLabelApi($labelID, $storeRow,$user_id);
    }
    public function updateLabel(int $labelID, array $data): void
    {
        // Input validation
        $this->validator->validateLabelUpdate($labelID, $data);

        // Map form data to row
        $storeRow = $this->mapToLabelRow($data);

        // Insert store
        $this->repository->updateLabel($labelID, $storeRow);
    }
    
    
    public function deleteLabel(int $labelID, array $data): void
    {
        $this->repository->deleteLabel($labelID);
    }

    public function deleteLabelAll( int $lotId, array $data): void
    {
        $this->repository->deleteLabelAll($lotId);
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
        if (isset($data['split_label_id'])) {
            $result['split_label_id'] = (string)$data['split_label_id'];
        }


        return $result;
    }
}
