<?php

namespace App\Domain\Label\Service;

use App\Domain\Label\Repository\LabelRepository;
use App\Domain\Label\Service\LabelFinder;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Service.
 */
final class LabelUpdater
{
    private $repository;
    private $validator;
    private $finder;
    private $session;

    public function __construct(
        LabelRepository $repository,
        LabelValidator $validator,
        LabelFinder $finder,
        Session $session,
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->finder = $finder;
        $this->session = $session;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function insertLabel(array $data): int
    {

        $this->validator->validateLabelInsert($data);

        $labelRow = $this->mapToLabelRow($data);

        $id = $this->repository->insertLabel($labelRow);


        return $id;
    }


    public function insertLabelApi(array $data, $user_id): int //สร้าง labels จาก splitlabel
    {

        $this->validator->validateLabelInsert($data);

        $Row = $this->mapToLabelRow($data);

        $id = $this->repository->insertLabelApi($Row, $user_id);


        return $id;
    }

    
    // public function updateLabelStatusApi(int $labelID, array $data, $user_id): void
    // {
    //     $this->validator->validateLabelUpdate($labelID, $data);

    //     $Row = $this->mapToLabelRow($data);
    //     $Row['status'] = "VOID";
    //     $Row['label_void_reason_id'] = 2;

    //     $this->repository->updateLabelmergeApi($labelID, $Row, $user_id);
    // }

    public function updateLabelStatus(int $labelID, array $data, $user_id): void
    {
        $this->validator->validateLabelUpdate($labelID, $data);

        $Row = $this->mapToLabelRow($data);

        //web
        if ($data['up_status']=="SELLING") {
            $Row['status'] = "SELLING";
        }else if ($data['up_status']=="PACKED"){
            $Row['status'] = "PACKED";
        }else if($data['up_status']=="VOID"){
            if ($data['void']=="MERGED") {
                $Row['status'] = "VOID";
                $Row['label_void_reason_id'] = 2;
            }
        }else if($data['up_status']=="USED"){
            $Row['status'] = "USED";
        }

        $this->repository->updateLabelApi($labelID, $Row, $user_id);
    }

    public function updateLabelApi(int $labelID, array $data, $user_id): void
    {
        $this->validator->validateLabelUpdate($labelID, $data);

        $Row = $this->mapToLabelRow($data);
        $Row['merge_pack_id']=$data['merge_pack_id'];

        $this->repository->updateLabelApi($labelID, $Row, $user_id);
    }

    public function updateCancelLabelApi(int $id, array $data, $user_id): void
    {
        $this->validator->validateLabelUpdate($id, $data);

        $Row = $this->mapToLabelRow($data);
        if ($data[0]['merge_pack_id'] == $data['check_mp_id'] && ($data[0]['label_type'] == "NONFULLY" || $data[0]['label_type'] == "MERGE_NONFULLY")) {
            $Row['status'] = "PACKED";
        } else {
            if (($data[0]['label_type'] == "NONFULLY" || $data[0]['label_type'] == "MERGE_NONFULLY") && $data[0]['status'] != "MERGED") {
                $Row['merge_pack_id'] = $data[0]['merge_pack_id'];
                $Row['status'] = "PACKED";
            } else {
                $Row['merge_pack_id'] = $data[0]['merge_pack_id'];
                $Row['status'] = $data[0]['status'];
            }
        }
        $this->repository->updateLabelApi($id, $Row, $user_id);
    }
    public function updateLabel(int $labelID, array $data): void
    {
        $this->validator->validateLabelUpdate($labelID, $data);

        $Row = $this->mapToLabelRow($data);

        $this->repository->updateLabel($labelID, $Row);
    }
    
    public function deleteLabel(int $labelID, array $data): void
    {
        $this->repository->deleteLabel($labelID);
    }

    public function deleteLabelAll(int $lotId, array $data): void
    {
        $this->repository->deleteLabelAll($lotId);
    }

    public function insertLabelMergePackApi(array $data, $user_id): int
    {
        $this->validator->validateLabelInsert($data);

        $lotRow = $this->mapToLabelRow($data);

        $id = $this->repository->insertLabelMergePackApi($lotRow, $user_id);

        return $id;
    }

    public function updateLabelMergePackApi(int $labelId, array $data, $user_id): void
    {

        $this->validator->validateLabelUpdate($labelId, $data);

        $storeRow = $this->mapToLabelRow($data);

        $this->repository->updateLabelMergePackApi($labelId, $storeRow, $user_id);
    }

    public function updateLabelStatusMerging(int $ID, array $data, $user_id): void
    {
        $this->validator->validateLabelUpdate($ID, $data);

        $Row = $this->mapToLabelRow($data);
        $ID = $data[0]['id'];
        // $Row['merge_pack_id']=$data['check_mp_id'];
        if ($data[0]['status'] == "PACKED" && ($data[0]['label_type'] == "NONFULLY" || $data[0]['label_type'] == "MERGE_NONFULLY")) {
            if ($data[0]['merge_pack_id'] == 0) {
                if ($data[0]['status'] == "MERGED") {
                    $Row['status'] = $data[0]['status'];
                    $Row['merge_pack_id'] = $data[0]['merge_pack_id'];
                } else if ($data[0]['status'] == "PACKED") {
                    $Row['status'] = "MERGING";
                    $Row['merge_pack_id'] = $data[0]['merge_pack_id'];
                }
            } else {
                $Row['status'] = $data[0]['status'];
                $Row['merge_pack_id'] = $data[0]['merge_pack_id'];
            }
            if ($data[0]['label_type'] == "MERGE_NONFULLY") {
                $Row['status'] = "MERGING";
                $Row['merge_pack_id'] = $data[0]['merge_pack_id'];
            }
        }

        $this->repository->updateLabelMerging($ID, $Row, $user_id);
    }

    public function updateLabelDefalt(int $ID, array $data, $user_id): void
    {
        $this->validator->validateLabelUpdate($ID, $data);

        $Row = $this->mapToLabelRow($data);
        $Row['status'] = "PACKED";
        $Row['merge_pack_id'] = 0;

        $this->repository->updateLabeldefault($ID, $Row, $user_id);
    }

    public function updateLabelStatusPackedApi(int $labelID, array $data, $user_id): void
    {
        $this->validator->validateLabelUpdate($labelID, $data);

        $Row = $this->mapToLabelRow($data);
        $Row['status'] = "PACKED";

        $this->repository->updateLabelmergeApi($labelID, $Row, $user_id);
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
        if (isset($data['scan_no'])) {
            $result['scan_no'] = (string)$data['scan_no'];
        }
        if (isset($data['is_error'])) {
            $result['is_error'] = (string)$data['is_error'];
        }
        return $result;
    }

    public function genMergeLabel(array $data): array
    {
        $merge_pack_id = (int)($data['merge_pack_id'] ?? 1);
        $quantity = (int)$data['quantity'] ?? 1;
        $std_pack = (int)$data['std_pack'] ?? 1;
        $user_id = (int)$data['user_id'] ?? 1;
        $num_packs = ceil($quantity / $std_pack);
        $num_full_packs = floor($quantity / $std_pack);

        $labels = [];
        for ($i = 0; $i < $num_full_packs; $i++) {
            $data1['merge_pack_id'] = $merge_pack_id;
            $data1['label_type'] = "MERGE_FULLY";
            $data1['quantity'] = $std_pack;
            $data1['status'] = "CREATED";
            $id = $this->insertLabelApi($data1, $user_id);
            $data1['label_no'] = "P" . str_pad($id, 10, "0", STR_PAD_LEFT);
            $this->updateLabelApi($id, $data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabelCreateFromMerges($params2);
            array_push($labels, $rt1[0]);
        }
        if ($num_full_packs != $num_packs) {
            $data1['merge_pack_id'] = $merge_pack_id;
            $data1['label_type'] = "MERGE_NONFULLY";
            $data1['quantity'] = $quantity - ($num_full_packs * $std_pack);
            $data1['status'] = "CREATED";
            $id = $this->insertLabelApi($data1, $user_id);
            $data1['label_no'] = "P" . str_pad($id, 10, "0", STR_PAD_LEFT);
            $this->updateLabelApi($id, $data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabelCreateFromMerges($params2);
            array_push($labels, $rt1[0]);
        }
        // $rtdata['message']="Gen Merge Labels Successful";
        // $rtdata['error']=false;
        $rtdata['labels'] = $labels;

        return $rtdata;
    }


    
}
