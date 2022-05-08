<?php

namespace App\Domain\Pack\Service;

use App\Domain\Pack\Repository\PackRepository;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Service.
 */
final class PackUpdater
{
    private $repository;
    private $validator;
    private $session;

    public function __construct(
        PackRepository $repository,
        PackValidator $validator,
        Session $session
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->session = $session;
    }

    public function insertPack(array $data): int
    {
        $this->validator->validatePackInsert($data);

        $row = $this->mapToRow($data);

        $id = $this->repository->insertPack($row);
        $data1['pack_no'] = "K" . str_pad($id, 11, "0", STR_PAD_LEFT);
        $user_id = $this->session->get('user')["id"];
        $this->repository->updatePackApi($id, $data1, $user_id);
        return $id;
    }

    public function insertPackApi(array $data, $user_id): int
    {
        $this->validator->validatePackInsert($data);

        $row = $this->mapToRow($data);

        $id = $this->repository->insertPackApi($row, $user_id);
        $data1['pack_no'] = "K" . str_pad($id, 11, "0", STR_PAD_LEFT);
        $this->repository->updatePackApi($id, $data1, $user_id);
        return $id;
    }

    public function updatePackStatus(int $packId, array $data, $user_id): void
    {
        $this->validator->validatePackUpdate($packId, $data);

        $row = $this->mapToRow($data);
        if ($data['up_status'] == "SELECTING_CPO") {
            $row['pack_status'] = "SELECTING_CPO";
        } else if ($data['up_status'] == "SELECTED_CPO") {
            $row['pack_status'] = "SELECTED_CPO";
        } else if ($data['up_status'] == "SELECTING_LABEL") {
            $row['pack_status'] = "SELECTING_LABEL";
        } else if ($data['up_status'] == "CONFIRMED") {
            $row['pack_status'] = "CONFIRMED";
        } else if ($data['up_status'] == "CREATED") {
            $row['pack_status'] = "CREATED";
        } else if ($data['up_status'] == "TAGGED") {
            $row['pack_status'] = "TAGGED";
        } else if ($data['up_status'] == "PRINTED") {
            $row['pack_status'] = "PRINTED";
        } else if ($data['up_status'] == "COMPLETE") {
            $row['pack_status'] = "COMPLETE";
        }

        $this->repository->updatePackApi($packId, $row, $user_id);
    }
    public function updatePack(int $packId, array $data): void
    {
        $this->validator->validatePackUpdate($packId, $data);

        $row = $this->mapToRow($data);
        $user_id = $this->session->get('user')["id"];

        $this->repository->updatePackApi($packId, $row, $user_id);
    }
    public function updateConfirmPackApi(int $packId, array $data, $user_id): void
    {
        $this->validator->validatePackUpdate($packId, $data);

        $row = $this->mapToRow($data);

        $this->repository->updatePackApi($packId, $row, $user_id);
    }
    public function updatePackApi(int $packId, array $data, $user_id): void
    {
        $this->validator->validatePackUpdate($packId, $data, $user_id);

        $totalQty = 0;
        for ($i = 0; $i < count($data); $i++) {
            $totalQty += $data[$i]['pack_qty'];
        }

        $row = $this->mapToRow($data);
        $row['total_qty'] = $totalQty;

        $this->repository->updatePackApi($packId, $row, $user_id);
    }
    public function updatePackStatusSelectingCpo(int $productId, array $data): void
    {
        $this->validator->validatePackUpdate($productId, $data);

        $row = $this->mapToRow($data);
        $row['pack_status'] = "SELECTING_CPO";
        $row['total_qty'] = $data['total_qty'];
        $user_id = $this->session->get('user')["id"];

        $this->repository->updatePackApi($productId, $row, $user_id);
    }

    public function updatePackDeleteApi(int $packId, array $data, $user_id): void
    {
        $this->validator->validatePackUpdate($packId, $data, $user_id);

        $row = $this->mapToRow($data);

        $this->repository->updatePackApi($packId, $row, $user_id);
    }

    public function updatePackSyncApi(int $packId, array $data, $user_id): void
    {
        $this->validator->validatePackUpdate($packId, $data, $user_id);

        $row = $this->mapToRow($data);

        $this->repository->updatePackApi($packId, $row, $user_id);
    }

    public function deletePackApi(int $productId): void
    {
        $this->repository->deletePack($productId);
    }

    private function mapToRow(array $data): array
    {
        $result = [];

        if (isset($data['pack_no'])) {
            $result['pack_no'] = (string)$data['pack_no'];
        }
        if (isset($data['invoice_no'])) {
            $result['invoice_no'] = (string)$data['invoice_no'];
        }
        if (isset($data['pack_date'])) {
            $result['pack_date'] = (string)$data['pack_date'];
        }
        if (isset($data['product_id'])) {
            $result['product_id'] = (int)$data['product_id'];
        }
        if (isset($data['packing_id'])) {
            $result['packing_id'] = (int)$data['packing_id'];
        }
        if (isset($data['total_qty'])) {
            $result['total_qty'] = (int)$data['total_qty'];
        }
        if (isset($data['pack_status'])) {
            $result['pack_status'] = (string)$data['pack_status'];
        }
        if (isset($data['is_delete'])) {
            $result['is_delete'] = (string)$data['is_delete'];
        }

        return $result;
    }
}
