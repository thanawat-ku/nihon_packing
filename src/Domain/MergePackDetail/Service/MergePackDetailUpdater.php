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



        $Row = $this->mapToRow($data);
        $Row['merge_pack_id']=$data['check_mp_id'];
        $Row['label_id']=$data[0]['id'];

        // if ($data[0]['merge_pack_id'] == 0 ) {
        //     if ($data['check_product_id'] == $data[0]['product_id']) {
        //         if ($data[0]['label_type'] == "NONFULLY" || $data[0]['label_type'] == "MERGE_NONFULLY") {
        //             $Row['is_error']="N";
        //         }else{
        //             $Row['is_error']="Y";
        //         }
        //     }else{
        //         $Row['is_error']="Y";
        //     }
        // }else{
        //     $Row['is_error']="Y";
        // }

        

        

        



        // $count_data = count($data);

        // for ($i=0; $i < $count_data; $i++) { 
        //     $Row['merge_pack_id'] = $data['labels'][$i]['merge_pack_id'];
        //     $Row['label_id'] = $data['labels'][$i]['id'];
        $id = $this->repository->insertMergePackDetailApi($Row, $user_id);
        // }

        return $id;
    }

    public function insertMergePackDetailApi(array $data, $user_id): int
    {
        $this->validator->validateMergePackDetailInsert($data);

        $Row = $this->mapToRow($data);

        $count_data = count($data);

        for ($i=0; $i < count($data); $i++) { 
            $Row['merge_pack_id'] = $data['labels'][$i]['merge_pack_id'];
            $Row['label_id'] = $data['labels'][$i]['id'];
            $id = $this->repository->insertMergePackDetailApi($Row, $user_id);
        }

        return $id;
    }

    public function updateMergePackDetailApi(int $labelId, array $data, $user_id): void
    {
        // Input validation
        $this->validator->validateMergePackDetailUpdate($labelId, $data);

        // Map form data to row
        $storeRow = $this->mapToRow($data);

        // Insert store
        $this->repository->updateMergePackDetailApi($labelId, $storeRow, $user_id);
    }

    // public function updateLabelMergePackApi(int $labelId, array $data, $user_id): void
    // {
    //     // Input validation
    //     $this->validator->validateMergePackDetailUpdate($labelId, $data);

    //     // Map form data to row
    //     $storeRow = $this->mapToLabelRow($data);

    //     // Insert store
    //     $this->repository->updateLabelApi($labelId, $storeRow, $user_id);
    // }


    public function deleteLabelMergePackDetailApi(int $id): void
    {
        $this->repository->deleteLabelMergePackApi($id);
    }

    public function deleteMergePackDetailApi(int $id): void
    {
        $this->repository->deleteMergePackDetailApi($id);
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
        if (isset($data['is_error'])) {
            $result['is_error'] = (string)$data['is_error'];
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
