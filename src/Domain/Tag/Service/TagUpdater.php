<?php

namespace App\Domain\Tag\Service;

use App\Domain\Tag\Repository\TagRepository;
use App\Domain\Tag\Service\TagFinder;
use App\Domain\TagSerial\Service\TagSerialUpdater;


/**
 * Service.
 */
final class TagUpdater
{
    private $repository;
    private $validator;
    private $finder;
    private $updater;

    public function __construct(
        TagRepository $repository,
        TagValidator $validator,
        TagFinder $finder,
        TagSerialUpdater $updater
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->finder = $finder;
        $this->updater = $updater;
    }

    public function insertTag(array $data): int
    {
        $this->validator->validateTagInsert($data);

        $row = $this->mapToTagRow($data);

        $id = $this->repository->insertTag($row);

        return $id;
    }

    public function insertTagApi(array $data, $user_id): int
    {
        $this->validator->validateTagInsert($data);

        $row = $this->mapToTagRow($data);


        $id = $this->repository->insertTagApi($row, $user_id);

        return $id;
    }

    public function updateTagApi(int $tagID, array $data, $user_id): void
    {

        $this->validator->validateTagUpdate($tagID, $data);


        $row = $this->mapToTagRow($data);


        $this->repository->updateTagApi($tagID, $row, $user_id);
    }

    public function updateTagAllFromPackIDApi(int $PackID, array $data, $user_id): void
    {

        $this->validator->validateTagUpdate($PackID, $data);


        $row = $this->mapToTagRow($data);


        $this->repository->updateTagAllFromPackIDApi($PackID, $row, $user_id);
    }

    public function updateTag(int $tagID, array $data): void
    {
        $this->validator->validateTagUpdate($tagID, $data);

        $row = $this->mapToTagRow($data);

        $this->repository->updateTag($tagID, $row);
    }

    public function updateTagPrintFromPackID(int $packID, array $data): void
    {
        $this->validator->validateTagUpdate($packID, $data);

        $row = $this->mapToTagRow($data);

        $this->repository->updateTagPrintFromPackID($packID, $row);
    }

    public function updateTagPrintFromPackIDApi(int $packID, array $data, int $userID): void
    {
        $this->validator->validateTagUpdate($packID, $data);

        $row = $this->mapToTagRow($data);

        $this->repository->updateTagPrintFromPackIDApi($packID, $row, $userID);
    }

    public function updateTagFronPackID(int $packID, array $data): void
    {
        $this->validator->validateTagUpdate($packID, $data);

        $row = $this->mapToTagRow($data);

        $this->repository->updateTagFronPackID($packID, $row);
    }

    public function deleteTags(int $packID): void
    {
        $this->repository->deleteTags($packID);
    }

    private function mapToTagRow(array $data): array
    {
        $result = [];

        if (isset($data['tag_no'])) {
            $result['tag_no'] = $data['tag_no'];
        }
        if (isset($data['pack_id'])) {
            $result['pack_id'] = $data['pack_id'];
        }
        if (isset($data['printer_id'])) {
            $result['printer_id'] = $data['printer_id'];
        }
        if (isset($data['quantity'])) {
            $result['quantity'] = $data['quantity'];
        }
        if (isset($data['box_no'])) {
            $result['box_no'] = $data['box_no'];
        }
        if (isset($data['total_box'])) {
            $result['total_box'] = $data['total_box'];
        }
        if (isset($data['wait_print'])) {
            $result['wait_print'] = $data['wait_print'];
        }
        if (isset($data['status'])) {
            $result['status'] = $data['status'];
        }

        return $result;
    }

    public function genTagsApi(int $packID, array $data, int $user_id): array
    {
        $totalQty = $data[0]['total_qty'];
        $stdBox = $data[0]['std_box'];

        $totalBox = ceil($totalQty / $stdBox);

        $tags = [];

        for ($i = 0; $i < $totalBox; $i++) {
            if ($i != $totalBox - 1 && $totalBox > 1) {
                $quantity = $totalBox - (($totalBox - 1) * $stdBox);
                $dataTag['pack_id'] = $packID;
                $dataTag['quantity'] = $stdBox;
                $dataTag['box_no'] = $i + 1;
                $dataTag['total_box'] = $totalBox;
                $dataTag['status'] = 'PRINTED';
                $dataTag['wait_print'] = 'N';
                $dataTag['printer_id'] = $data['printer_id'];
                $id = $this->insertTagApi($dataTag, $user_id);
                $dataTag['tag_no'] = "T" . str_pad($id, 11, "0", STR_PAD_LEFT);
                $this->updatetagApi($id, $dataTag, $user_id);
                $params['tag_id'] = $id;
                $rtTag = $this->finder->findTags($params);
                $customerId=$rtTag[0]["customer_id"];
                $params1['tag_id']=$id;
                $params1['customer_id']=$customerId;
                $this->updater->insertTagSerial($params1);
                array_push($tags, $rtTag[0]);
            } else if ($i == ($totalBox - 1) || $totalBox < 1) {
                $quantity = $totalQty - (($totalBox - 1) * $stdBox);
                $dataTag['pack_id'] = $packID;
                $dataTag['quantity'] = $quantity;
                $dataTag['box_no'] = $i + 1;
                $dataTag['total_box'] = $totalBox;
                $dataTag['status'] = 'PRINTED';
                $dataTag['wait_print'] = 'N';
                $dataTag['printer_id'] = $data['printer_id'];
                $id = $this->insertTagApi($dataTag, $user_id);
                $dataTag['tag_no'] = "T" . str_pad($id, 11, "0", STR_PAD_LEFT);
                $this->updatetagApi($id, $dataTag, $user_id);
                $params['tag_id'] = $id;
                $rtTag = $this->finder->findTags($params);
                $customerId=$rtTag[0]["customer_id"];
                $params1['tag_id']=$id;
                $params1['customer_id']=$customerId;
                $this->updater->insertTagSerial($params1);
                array_push($tags, $rtTag[0]);
            }
        }

        $rtdata['tags'] = $tags;

        return $rtdata;
    }

    public function genTags(int $packID, array $data): array
    {
        $totalQty = $data[0]['total_qty'];
        $stdBox = $data[0]['std_box'];
        $totalBox = ceil($totalQty / $stdBox);

        $tags = [];

        for ($i = 0; $i < $totalBox; $i++) {
            if ($i != $totalBox - 1 && $totalBox > 1) {
                $quantity = $totalBox - (($totalBox - 1) * $stdBox);
                $dataTag['pack_id'] = $packID;
                $dataTag['quantity'] = $stdBox;
                $dataTag['box_no'] = $i + 1;
                $dataTag['total_box'] = $totalBox;
                $dataTag['status'] = 'PRINTED';
                $dataTag['wait_print'] = 'N';
                $dataTag['printer_id'] = $data['printer_id'];
                $id = $this->insertTag($dataTag);
                $dataTag['tag_no'] = "T" . str_pad($id, 11, "0", STR_PAD_LEFT);
                $this->updatetag($id, $dataTag);
                $params['tag_id'] = $id;
                $rtTag = $this->finder->findTags($params);
                $customerId=$rtTag[0]["customer_id"];
                $params1['tag_id']=$id;
                $params1['customer_id']=$customerId;
                $this->updater->insertTagSerial($params1);
                array_push($tags, $rtTag[0]);
            } else if ($i == ($totalBox - 1) || $totalBox < 1) {
                $quantity = $totalQty - (($totalBox - 1) * $stdBox);
                $dataTag['pack_id'] = $packID;
                $dataTag['quantity'] = $quantity;
                $dataTag['box_no'] = $i + 1;
                $dataTag['total_box'] = $totalBox;
                $dataTag['status'] = 'PRINTED';
                $dataTag['wait_print'] = 'N';
                $dataTag['printer_id'] = $data['printer_id'];
                $id = $this->insertTag($dataTag);
                $dataTag['tag_no'] = "T" . str_pad($id, 11, "0", STR_PAD_LEFT);
                $this->updatetag($id, $dataTag);
                $params['tag_id'] = $id;
                $rtTag = $this->finder->findTags($params);
                $customerId=$rtTag[0]["customer_id"];
                $params1['tag_id']=$id;
                $params1['customer_id']=$customerId;
                $this->updater->insertTagSerial($params1);
                array_push($tags, $rtTag[0]);
            }
        }

        $rtdata['tags'] = $tags;

        return $rtdata;
    }
}
