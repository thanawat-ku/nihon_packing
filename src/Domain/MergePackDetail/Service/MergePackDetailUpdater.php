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

    public function insertMergePackDetailCheckApi(array $data, $user_id): int
    {
        $this->validator->validateMergePackDetailInsert($data);

        $row = $this->mapToRow($data);
        // $row['merge_pack_id'] = $data['check_mp_id'];
        // $row['label_id'] = $data[0]['id'];
        // $row['label_id'] = $data['id'];

        $id = $this->repository->insertMergePackDetailApi($row, $user_id);

        return $id;
    }

    public function insertMergePackDetailFromScanApi(array $data, $user_id): int
    {
        $this->validator->validateMergePackDetailInsert($data);

        $row = $this->mapToRow($data);
        

        $id = $this->repository->insertMergePackDetailApi($row, $user_id);

        return $id;
    }

    public function insertMergePackDetail(array $data): int
    {
        $this->validator->validateMergePackDetailInsert($data);

        $row = $this->mapToRow($data);

        $id = $this->repository->insertMergePackDetail($row);

        return $id;
    }

    public function insertMergePackDetailApi(array $data, $user_id): int
    {
        $this->validator->validateMergePackDetailInsert($data);

        $row = $this->mapToRow($data);

        $count_data = count($data);

        for ($i = 0; $i < count($data); $i++) {
            $row['merge_pack_id'] = $data['labels'][$i]['merge_pack_id'];
            $row['label_id'] = $data['labels'][$i]['id'];
            $id = $this->repository->insertMergePackDetailApi($row, $user_id);
        }

        return $id;
    }

    public function updateMergePackDetailApi(int $labelId, array $data, $user_id): int
    {
        $this->validator->validateMergePackDetailInsert($data);

        // Map form data to row
        $storeRow = $this->mapToRow($data);

        $count_data = count($data);

        for ($i=0; $i < count($data); $i++) { 
            $row['merge_pack_id'] = $data['labels'][$i]['merge_pack_id'];
            $row['label_id'] = $data['labels'][$i]['id'];
            $id = $this->repository->insertMergePackDetailApi($row, $user_id);
        }

        return $id;
    }

    // public function updateMergePackDetail(int $mergeId, array $data): void
    // {
    //     // Input validation
    //     $this->validator->validateMergePackDetailUpdate($mergeId, $data);

    //     // Map form data to row
    //     $storeRow = $this->mapToRow($data);

    //     // Insert store
    //     $this->repository->updateMergePackDetail($mergeId, $storeRow);
    // }

    public function deleteLabelMergePackDetailApi(int $id): void
    {
        $this->repository->deleteLabelMergePackApi($id);
    }

    public function deleteMergePackDetailApi(int $id): void
    {
        $this->repository->deleteMergePackDetailApi($id);
    }

    public function deleteMergePackDetail(int $mergeId): void
    {
        $this->repository->deleteMergePackDetail($mergeId);
    }

    public function deleteMergePackDetailFromLabel(int $labelId): void
    {
        $this->repository->deleteMergePackDetailFromLabel($labelId);
    }



    private function mapToRow(array $data): array
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

    // private function mapToLabelRow(array $data): array
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
