<?php

namespace App\Domain\Sell\Service;

use App\Domain\Sell\Repository\SellRepository;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Service.
 */
final class SellUpdater
{
    private $repository;
    private $validator;
    private $session;

    public function __construct(
        SellRepository $repository,
        SellValidator $validator,
        Session $session
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->session = $session;
    }

    public function insertSell(array $data): int
    {
        $this->validator->validateSellInsert($data);

        $row = $this->mapToRow($data);

        $id = $this->repository->insertSell($row);
        $data1['sell_no'] = "C" . str_pad($id, 11, "0", STR_PAD_LEFT);
        $user_id = $this->session->get('user')["id"];
        $this->repository->updateSellApi($id, $data1, $user_id);
        return $id;
    }

    public function insertSellApi(array $data, $user_id): int
    {
        $this->validator->validateSellInsert($data);

        $row = $this->mapToRow($data);
        $row['product_id'] = $data['product_id'];

        $id = $this->repository->insertSellApi($row, $user_id);
        $data1['sell_no'] = "C" . str_pad($id, 11, "0", STR_PAD_LEFT);
        $this->repository->updateSellApi($id, $data1, $user_id);
        return $id;
    }

    public function updateSellStatus(int $sellId, array $data, $user_id): void
    {
        $this->validator->validateSellUpdate($sellId, $data);

        $row = $this->mapToRow($data);
        if ($data['up_status'] == "SELECTING_CPO") {
            $row['sell_status'] = "SELECTING_CPO";
        } else if ($data['up_status'] == "SELECTED_CPO") {
            $row['sell_status'] = "SELECTED_CPO";
        } else if ($data['up_status'] == "SELECTING_LABEL") {
            $row['sell_status'] = "SELECTING_LABEL";
        } else if ($data['up_status'] == "CONFIRMED") {
            $row['sell_status'] = "CONFIRMED";
        } else if ($data['up_status'] == "CREATED") {
            $row['sell_status'] = "CREATED";
        } else if ($data['up_status'] == "TAGGED") {
            $row['sell_status'] = "TAGGED";
        } else if ($data['up_status'] == "PRINTED") {
            $row['sell_status'] = "PRINTED";
        } else if ($data['up_status'] == "COMPLETE") {
            $row['sell_status'] = "COMPLETE";
        }

        $this->repository->updateSellApi($sellId, $row, $user_id);
    }
    public function updateSell(int $sellId, array $data): void
    {
        $this->validator->validateSellUpdate($sellId, $data);

        $row = $this->mapToRow($data);
        $user_id = $this->session->get('user')["id"];

        $this->repository->updateSellApi($sellId, $row, $user_id);
    }
    public function updateConfirmSellApi(int $sellId, array $data, $user_id): void
    {
        $this->validator->validateSellUpdate($sellId, $data);

        $row = $this->mapToRow($data);

        $this->repository->updateSellApi($sellId, $row, $user_id);
    }
    public function updateSellApi(int $sellId, array $data, $user_id): void
    {
        $this->validator->validateSellUpdate($sellId, $data, $user_id);

        $totalQty = 0;
        for ($i = 0; $i < count($data); $i++) {
            $totalQty += $data[$i]['sell_qty'];
        }

        $row = $this->mapToRow($data);
        $row['total_qty'] = $totalQty;

        $this->repository->updateSellApi($sellId, $row, $user_id);
    }
    public function updateSellStatusSelectingCpo(int $productId, array $data): void
    {
        $this->validator->validateSellUpdate($productId, $data);

        $row = $this->mapToRow($data);
        $row['sell_status'] = "SELECTING_CPO";
        $row['total_qty'] = $data['total_qty'];
        $user_id = $this->session->get('user')["id"];

        $this->repository->updateSellApi($productId, $row, $user_id);
    }

    public function updateSellDeleteApi(int $sellId, array $data, $user_id): void
    {
        $this->validator->validateSellUpdate($sellId, $data, $user_id);

        $row = $this->mapToRow($data);

        $this->repository->updateSellApi($sellId, $row, $user_id);
    }

    public function updateSellSyncApi(int $sellId, array $data, $user_id): void
    {
        $this->validator->validateSellUpdate($sellId, $data, $user_id);

        $row = $this->mapToRow($data);

        $this->repository->updateSellApi($sellId, $row, $user_id);
    }

    public function deleteSellApi(int $productId): void
    {
        $this->repository->deleteSell($productId);
    }

    private function mapToRow(array $data): array
    {
        $result = [];

        if (isset($data['sell_no'])) {
            $result['sell_no'] = (string)$data['sell_no'];
        }
        if (isset($data['invoice_no'])) {
            $result['invoice_no'] = (string)$data['invoice_no'];
        }
        if (isset($data['sell_date'])) {
            $result['sell_date'] = (string)$data['sell_date'];
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
        if (isset($data['sell_status'])) {
            $result['sell_status'] = (string)$data['sell_status'];
        }
        if (isset($data['is_delete'])) {
            $result['is_delete'] = (string)$data['is_delete'];
        }

        return $result;
    }
}
