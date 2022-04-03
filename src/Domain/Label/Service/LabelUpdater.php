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
        Session $session
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
        $row = $this->mapToLabelRow($data);

        $data['label_no'] = "X" . str_pad(1, 11, "0", STR_PAD_LEFT);

        $this->validator->validateLabelInsert($data);

        $id = $this->repository->insertLabelApi($row, $user_id);

        $data1['label_no'] = "P" . str_pad($id, 11, "0", STR_PAD_LEFT);
        $this->updateLabelApi($id, $data1, $user_id);

        return $id;
    }

    public function updateLabelStatus(int $labelID, array $data, $user_id): void
    {
        $this->validator->validateLabelUpdate($labelID, $data);

        // $data1['label_no'] = "P" . str_pad($id, 11, "0", STR_PAD_LEFT);
        // $this->updateLabelApi($id, $data1, $user_id);
        $row = $this->mapToLabelRow($data);

        //web
        if ($data['up_status'] == "SELLING") {
            $row['status'] = "SELLING";
        } else if ($data['up_status'] == "PACKED") {
            $row['status'] = "PACKED";
        } else if ($data['up_status'] == "VOID") {
            if ($data['void'] == "MERGED") {
                $row['status'] = "VOID";
                $row['label_void_reason_id'] = 2;
            }
        } else if ($data['up_status'] == "USED") {
            $row['status'] = "USED";
        }

        $this->repository->updateLabelApi($labelID, $row, $user_id);
    }

    public function registerLabel(int $lot_id, array $data): void
    {
        $this->validator->validateLabelUpdate($lot_id, $data);

        $row = $this->mapToLabelRow($data);

        $this->repository->registerLabel($lot_id, $row);
    }

    public function registerLabelApi(int $lot_id, array $data, $user_id): void
    {
        $this->validator->validateLabelUpdate($lot_id, $data);

        $row = $this->mapToLabelRow($data);

        $this->repository->registerLabelApi($lot_id, $row, $user_id);
    }

    public function registerMerge(int $mergeId, array $data): void
    {
        // Input validation
        $this->validator->validateLabelUpdate($mergeId, $data);

        //     // Map form data to row
        $row = $this->mapToLabelRow($data);

        // Insert store
        $this->repository->registerMerge($mergeId, $row);
    }

    public function updateLabelApi(int $labelID, array $data, $user_id): void
    {
        $this->validator->validateLabelUpdate($labelID, $data);

        $row = $this->mapToLabelRow($data);

        $this->repository->updateLabelApi($labelID, $row, $user_id);
    }

    public function updateCancelLabelApi(int $id, array $data, $user_id): void
    {
        $this->validator->validateLabelUpdate($id, $data);

        $row = $this->mapToLabelRow($data);
        if ($data[0]['merge_pack_id'] == $data['check_mp_id'] && ($data[0]['label_type'] == "NONFULLY" || $data[0]['label_type'] == "MERGE_NONFULLY")) {
            $row['status'] = "PACKED";
        } else {
            if (($data[0]['label_type'] == "NONFULLY" || $data[0]['label_type'] == "MERGE_NONFULLY") && $data[0]['status'] != "MERGED") {
                $row['merge_pack_id'] = $data[0]['merge_pack_id'];
                if ($data[0]['status'] == "MERGING") {
                    $row['status'] = "PACKED";
                }
            } else {
                $row['merge_pack_id'] = $data[0]['merge_pack_id'];
                $row['status'] = $data[0]['status'];
            }
        }
        $this->repository->updateLabelApi($id, $row, $user_id);
    }

    public function updateLabel(int $labelID, array $data): void
    {
        $this->validator->validateLabelUpdate($labelID, $data);

        $row = $this->mapToLabelRow($data);

        $this->repository->updateLabel($labelID, $row);
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

        $row = $this->mapToLabelRow($data);

        $this->repository->updateLabelMergePackApi($labelId, $row, $user_id);
    }

    public function updateLabelStatusMerging(int $ID, array $data, $user_id): void
    {
        $this->validator->validateLabelUpdate($ID, $data);

        $row = $this->mapToLabelRow($data);
        $ID = $data[0]['id'];
        // $row['merge_pack_id']=$data['check_mp_id'];
        if ($data[0]['status'] == "PACKED" && ($data[0]['label_type'] == "NONFULLY" || $data[0]['label_type'] == "MERGE_NONFULLY")) {
            if ($data[0]['merge_pack_id'] == 0) {
                if ($data[0]['status'] == "MERGED") {
                    $row['status'] = $data[0]['status'];
                    // $row['merge_pack_id'] = $data[0]['merge_pack_id'];
                } else if ($data[0]['status'] == "PACKED") {
                    $row['status'] = "MERGING";
                    // $row['merge_pack_id'] = $data[0]['merge_pack_id'];
                }
            } else {
                $row['status'] = $data[0]['status'];
                // $row['merge_pack_id'] = $data[0]['merge_pack_id'];
            }
            if ($data[0]['label_type'] == "MERGE_NONFULLY") {
                $row['status'] = "MERGING";
                // $row['merge_pack_id'] = $data[0]['merge_pack_id'];
            }
        }

        $this->repository->updateLabelMerging($ID, $row, $user_id);
    }

    public function updateLabelDefalt(int $ID, array $data, $user_id): void
    {
        $this->validator->validateLabelUpdate($ID, $data);

        $row = $this->mapToLabelRow($data);
        $row['status'] = "PACKED";
        $row['merge_pack_id'] = 0;

        $this->repository->updateLabeldefault($ID, $row, $user_id);
    }

    public function registerLabelFromMergePackIDApi(int $mergePackID, array $data, $user_id): void
    {
        $this->validator->validateLabelUpdate($mergePackID, $data);

        $row = $this->mapToLabelRow($data);

        $this->repository->registerLabelFromMergePackIDApi($mergePackID, $row, $user_id);
    }


    private function mapToLabelRow(array $data): array
    {
        $result = [];

        if (isset($data['lot_id'])) {
            $result['lot_id'] = (string)$data['lot_id'];
        }
        if (isset($data['prefer_lot_id'])) {
            $result['prefer_lot_id'] = (string)$data['prefer_lot_id'];
        }
        if (isset($data['product_id'])) {
            $result['product_id'] = (string)$data['product_id'];
        }
        if (isset($data['printer_id'])) {
            $result['printer_id'] = (string)$data['printer_id'];
        }
        if (isset($data['prefer_lot_id'])) {
            $result['prefer_lot_id'] = (string)$data['prefer_lot_id'];
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
        if (isset($data['scan_no'])) {
            $result['scan_no'] = (string)$data['scan_no'];
        }
        if (isset($data['is_error'])) {
            $result['is_error'] = (string)$data['is_error'];
        }
        if (isset($data['is_delete'])) {
            $result['is_delete'] = (string)$data['is_delete'];
        }
        if (isset($data['wait_print'])) {
            $result['wait_print'] = (string)$data['wait_print'];
        }
        return $result;
    }

    public function genMergeLabel(array $data): array
    {
        $merge_pack_id = (int)($data['merge_pack_id'] ?? 1);
        $quantity = (int)$data['quantity'] ?? 1;
        $std_pack = (int)$data['std_pack'] ?? 1;
        $user_id = (int)$data['user_id'] ?? 1;
        $productID = (int)$data['product_id'] ?? 0;
        $printerID = (int)$data['printer_id'] ?? 1;
        $preferLotID = $data['prefer_lot_id'] ?? 0;
        $waitPrint = $data['wait_print'] ?? "N";
        $lotId = $data['lot_id'] ?? 0;
        // $perferLotID = $data['']
        $num_packs = ceil($quantity / $std_pack);
        $num_full_packs = floor($quantity / $std_pack);

        $labels = [];
        for ($i = 0; $i < $num_full_packs; $i++) {
            $data1['merge_pack_id'] = $merge_pack_id;
            $data1['label_type'] = "MERGE_FULLY";
            $data1['quantity'] = $std_pack;
            $data1['status'] = "PRINTED";
            $data1['product_id'] = $productID;
            $data1['printer_id'] = $printerID;
            $data1['prefer_lot_id'] = $preferLotID;
            $data1['wait_print'] = $waitPrint;
            $data1['lot_id'] = $lotId;
            $id = $this->insertLabelApi($data1, $user_id);
            $data1['label_no'] = "P" . str_pad($id, 11, "0", STR_PAD_LEFT);
            $this->updateLabelApi($id, $data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabelCreateFromMerges($params2);
            array_push($labels, $rt1[0]);
        }
        if ($num_full_packs != $num_packs) {
            $data1['merge_pack_id'] = $merge_pack_id;
            $data1['label_type'] = "MERGE_NONFULLY";
            $data1['quantity'] = $quantity - ($num_full_packs * $std_pack);
            $data1['status'] = "PRINTED";
            $data1['product_id'] = $productID;
            $data1['prefer_lot_id'] = $preferLotID;
            $data1['printer_id'] = $printerID;
            $data1['wait_print'] = $waitPrint;
            $data1['lot_id'] = $lotId;
            $id = $this->insertLabelApi($data1, $user_id);
            $data1['label_no'] = "P" . str_pad($id, 11, "0", STR_PAD_LEFT);
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

    public function genLabelNo(array $data): array
    {
        // $merge_status = (string)($data['merge_status'] ?? '');
        $lot_id = (int)($data['lot_id'] ?? 1);
        $real_qty = (int)$data['real_qty'] ?? 1;
        $std_pack = (int)$data['std_pack'] ?? 1;
        $productId = (int)$data['product_id'] ?? 1;
        $user_id = (int)$data['user_id'] ?? 1;
        $printerID = (int)$data['printer_id'] ?? 1;
        $waitPrint = $data['wait_print'] ?? "N";
        $num_packs = ceil($real_qty / $std_pack);
        $num_full_packs = floor($real_qty / $std_pack);

        $labels = [];
        for ($i = 0; $i < $num_full_packs; $i++) {
            $data1['lot_id'] = $lot_id;
            $data1['label_type'] = "FULLY";
            $data1['quantity'] = $std_pack;
            $data1['status'] = "PRINTED";
            $data1['product_id'] = $productId;
            $data1['printer_id'] = $printerID;
            $data1['prefer_lot_id'] = $lot_id;
            $data1['wait_print'] = $waitPrint;
            $id = $this->insertLabelApi($data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabels($params2);
            array_push($labels, $rt1[0]);
        }
        if ($num_full_packs != $num_packs) {
            $data1['lot_id'] = $lot_id;
            $data1['label_type'] = "NONFULLY";
            $data1['quantity'] = $real_qty - ($num_full_packs * $std_pack);
            $data1['status'] = "PRINTED";
            $data1['product_id'] = $productId;
            $data1['printer_id'] = $printerID;
            $data1['prefer_lot_id'] = $lot_id;
            $data1['wait_print'] = $waitPrint;
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
        $productId = (int)$data['product_id'] ?? 0;
        $printerId = (int)$data['printer_id'] ?? 0;
        $waitPrint = $data['wait_print'] ?? "N";
        $perferLotID = (int)$data['prefer_lot_id'] ?? 0;

        $labels = [];
        if ($label_type == "FULLY" || $label_type == "NONFULLY") {
            $data1['lot_id'] = $lot_id;
            $data1['prefer_lot_id'] = $perferLotID;
            $data1['label_type'] = "NONFULLY";
            $data1['quantity'] = $quantity1;
            $data1['status'] = "PRINTED";
            $data1['product_id'] = $productId;
            $data1['printer_id'] = $printerId;
            $data1['wait_print'] = $waitPrint;
            $data1['prefer_lot_id'] = $perferLotID;
            $id = $this->insertLabelApi($data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabels($params2);
            array_push($labels, $rt1[0]);

            $data1['lot_id'] = $lot_id;
            $data1['prefer_lot_id'] = $perferLotID;
            $data1['label_type'] = "NONFULLY";
            $data1['quantity'] = $quantity2;
            $data1['status'] = "PRINTED";
            $data1['product_id'] = $productId;
            $data1['printer_id'] = $printerId;
            $data1['wait_print'] = $waitPrint;
            $data1['prefer_lot_id'] = $perferLotID;
            $id = $this->insertLabelApi($data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabels($params2);
            array_push($labels, $rt1[0]);
        } else if ($label_type == "MERGE_FULLY" || $label_type == "MERGE_NONFULLY") {
            $data1['merge_pack_id'] = $merge_pack_id;
            $data1['prefer_lot_id'] = $perferLotID;
            $data1['label_type'] = "MERGE_NONFULLY";
            $data1['quantity'] = $quantity1;
            $data1['status'] = "PRINTED";
            $data1['product_id'] = $productId;
            $data1['printer_id'] = $printerId;
            $data1['wait_print'] = $waitPrint;
            $id = $this->insertLabelApi($data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabelForLotZero($params2);
            array_push($labels, $rt1[0]);

            $data1['merge_pack_id'] = $merge_pack_id;
            $data1['prefer_lot_id'] = $perferLotID;
            $data1['label_type'] = "MERGE_NONFULLY";
            $data1['quantity'] = $quantity2;
            $data1['status'] = "PRINTED";
            $data1['product_id'] = $productId;
            $data1['printer_id'] = $printerId;
            $data1['wait_print'] = $waitPrint;
            $id = $this->insertLabelApi($data1, $user_id);
            $params2['label_id'] = $id;
            $rt1 = $this->finder->findLabelForLotZero($params2);
            array_push($labels, $rt1[0]);
        }

        return $labels;
    }
}
