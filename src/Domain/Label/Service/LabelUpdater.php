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

    public function insertLabel(array $data): int
    {

        $this->validator->validateLabelInsert($data);

        $labelRow = $this->mapToLabelRow($data);

        $id = $this->repository->insertLabel($labelRow);


        return $id;
    }



    public function insertLabelerror(array $data, $user_id): int
    {

        $this->validator->validateLabelInsert($data);

        // Map form data to row
        $lotRow = $this->mapToMergePackRow($data);

        // Insert transferStore
        $id = $this->repository->insertLabelerror($lotRow, $user_id);


        return $id;
    }


    public function insertLabelApi(array $data, $user_id): int //สร้าง labels จาก splitlabel
    {

        $data['label_no'] = "X" . str_pad(1, 11, "0", STR_PAD_LEFT);

        $this->validator->validateLabelInsert($data);

        $Row = $this->mapToLabelRow($data);

        $id = $this->repository->insertLabelApi($Row, $user_id);

        $data1['label_no'] = "P" . str_pad($id, 11, "0", STR_PAD_LEFT);
        $this->updateLabelApi($id, $data1, $user_id);

        return $id;
    }

    public function registerLabel(int $lot_id, array $data): void
    {
        // Input validation
        $this->validator->validateLabelUpdate($lot_id, $data);

        //     // Map form data to row
        $storeRow = $this->mapToLabelRow($data);

        // Insert store
        $this->repository->registerLabel($lot_id, $storeRow);
    }

    public function registerLabelApi(int $lot_id, array $data, $user_id): void
    {
        // Input validation
        $this->validator->validateLabelUpdate($lot_id, $data);

        //     // Map form data to row
        $storeRow = $this->mapToLabelRow($data);

        // Insert store
        $this->repository->registerLabelApi($lot_id, $storeRow, $user_id);
    }

    public function updateLabelApi(int $labelID, array $data, $user_id): void
    {
        // Input validation
        $this->validator->validateLabelUpdate($labelID, $data);

        //     // Map form data to row
        $storeRow = $this->mapToLabelRow($data);

        // Insert store
        $this->repository->updateLabelApi($labelID, $storeRow, $user_id);
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

    public function deleteLabelAll(int $lotId, array $data): void
    {
        $this->repository->deleteLabelAll($lotId);
    }
    public function insertLabelMergePackApi(array $data, $user_id): int
    {
        // Input validation
        $this->validator->validateLabelInsert($data);

        // Map form data to row
        $lotRow = $this->mapToLabelRow($data);

        // Insert transferStore
        $id = $this->repository->insertLabelMergePackApi($lotRow, $user_id);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
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
        if (isset($data['label_void_reason_id'])) {
            $result['label_void_reason_id'] = (string)$data['label_void_reason_id'];
        }
        
        return $result;
    }

    //----merge label-----
    private function mapToMergePackRow(array $data): array
    {
        $result = [];

        if (isset($data['merge_no'])) {
            $result['merge_no'] = (string)$data['merge_no'];
        }
        if (isset($data['product_id'])) {
            $result['product_id'] = (int)$data['product_id'];
        }
        if (isset($data['merge_status'])) {
            $result['merge_status'] = (string)$data['merge_status'];
        }
        return $result;
    }

    public function genLabelNo(array $data): array
    {
        // $merge_status = (string)($data['merge_status'] ?? '');
        $lot_id = (int)($data['lot_id'] ?? 1);
        $real_qty = (int)$data['real_qty'] ?? 1;
        $std_pack = (int)$data['std_pack'] ?? 1;
        $user_id = (int)$data['user_id'] ?? 1;
        $num_packs = ceil($real_qty / $std_pack);
        $num_full_packs = floor($real_qty / $std_pack);

        $labels = [];
        for ($i = 0; $i < $num_full_packs; $i++) {
            $data1['lot_id'] = $lot_id;
            $data1['label_type'] = "FULLY";
            $data1['quantity'] = $std_pack;
            $data1['status'] = "CREATED";
            $id = $this->insertLabelApi($data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabels($params2);
            array_push($labels, $rt1[0]);
        }
        if ($num_full_packs != $num_packs) {
            $data1['lot_id'] = $lot_id;
            $data1['label_type'] = "NONFULLY";
            $data1['quantity'] = $real_qty - ($num_full_packs * $std_pack);
            $data1['status'] = "CREATED";
            $id = $this->insertLabelApi($data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabels($params2);
            array_push($labels, $rt1[0]);
        }
        return $labels;
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
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabels($params2);
            array_push($labels, $rt1[0]);
        }
        if ($num_full_packs != $num_packs) {
            $data1['merge_pack_id'] = $merge_pack_id;
            $data1['label_type'] = "MERGE_NONFULLY";
            $data1['quantity'] = $quantity - ($num_full_packs * $std_pack);
            $data1['status'] = "CREATED";
            $id = $this->insertLabelApi($data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabels($params2);
            array_push($labels, $rt1[0]);
        }
        
        return $labels;
    }

    public function genSplitLabel(array $data): array
    {
        $label_type = $data['label_type'] ?? "FULLY";
        $lot_id = (int)($data['lot_id'] ?? 1);
        $merge_pack_id = (int)($data['merge_pack_id'] ?? 1);
        $quantity1 = (int)$data['quantity1'] ?? 1;
        $quantity2 = (int)$data['quantity2'] ?? 1;
        $user_id = (int)$data['user_id'] ?? 1;

        $labels = [];
        if ($label_type == "FULLY" || $label_type == "NONFULLY") {
            $data1['lot_id'] = $lot_id;
            $data1['label_type'] = "NONFULLY";
            $data1['quantity'] = $quantity1;
            $data1['status'] = "CREATED";
            $id = $this->insertLabelApi($data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabels($params2);
            array_push($labels, $rt1[0]);

            $data1['lot_id'] = $lot_id;
            $data1['label_type'] = "NONFULLY";
            $data1['quantity'] = $quantity2;
            $data1['status'] = "CREATED";
            $id = $this->insertLabelApi($data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabels($params2);
            array_push($labels, $rt1[0]);
        } else if ($label_type == "MERGE_FULLY" || $label_type == "MERGE_NONFULLY") {
            $data1['merge_pack_id'] = $merge_pack_id;
            $data1['label_type'] = "MERGE_NONFULLY";
            $data1['quantity'] = $quantity1;
            $data1['status'] = "CREATED";
            $id = $this->insertLabelApi($data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabels($params2);
            array_push($labels, $rt1[0]);

            $data1['merge_pack_id'] = $merge_pack_id;
            $data1['label_type'] = "MERGE_NONFULLY";
            $data1['quantity'] = $quantity2;
            $data1['status'] = "CREATED";
            $id = $this->insertLabelApi($data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabels($params2);
            array_push($labels, $rt1[0]);
        }

        return $labels;
    }
}
