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
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function insertSell( array $data): int
    {
        $this->validator->validateSellInsert($data);

        $Row = $this->mapToRow($data);

        $id=$this->repository->insertSell($Row);
        $data1['sell_no']="C".str_pad($id, 10, "0", STR_PAD_LEFT);
        $user_id=$this->session->get('user')["id"];
        $this->repository->updateSellApi($id, $data1, $user_id);
        return $id;
    }

    public function insertSellApi(array $data, $user_id): int
    {
        $this->validator->validateSellInsert($data);

        $Row = $this->mapToRow($data);
        $Row['product_id']=$data['ProductID'];

        $id = $this->repository->insertSellApi($Row, $user_id);
        $data1['sell_no']="C".str_pad($id, 10, "0", STR_PAD_LEFT);
        $this->repository->updateSellApi($id, $data1, $user_id);
        return $id;
    }

    public function updateSellStatus(int $sellId, array $data, $user_id): void
    {
        $this->validator->validateSellUpdate($sellId, $data);

        $Row = $this->mapToRow($data);
        if($data['up_status'] == "SELECTED_CPO"){
            $Row['sell_status'] = "SELECTED_CPO";
        }else if($data['up_status'] == "SELECTED_LABEL"){
            $Row['sell_status'] = "SELECTED_LABEL";
        }

        $this->repository->updateSellApi($sellId, $Row, $user_id);
    }
    public function updateSell(int $sellId, array $data): void
    {
        $this->validator->validateSellUpdate($sellId, $data);

        $Row = $this->mapToRow($data);
        $user_id=$this->session->get('user')["id"];

        $this->repository->updateSellApi($sellId, $Row, $user_id);
    }
    public function updateSellApi(int $sellId, array $data, $user_id): void
    {
        $this->validator->validateSellUpdate($sellId, $data, $user_id);

        $totalQty = 0;
        for ($i = 0; $i < count($data); $i++) {
            $totalQty += $data[$i]['sell_qty'];
        }
        
        $Row = $this->mapToRow($data);
        $Row['total_qty'] = $totalQty;

        $this->repository->updateSellApi($sellId, $Row, $user_id);
    }
    public function updateSellStatusSelectedCpoApi(int $sellId, array $data, $user_id): void
    {
        $this->validator->validateSellUpdate($sellId, $data);

        $Row = $this->mapToRow($data);
        $Row['sell_status'] = "SELECTED_CPO";

        $this->repository->updateSellApi($sellId, $Row, $user_id);
    }

    public function updateSellSelectingLabelApi(int $sellId, array $data, $user_id): void
    {
        $this->validator->validateSellUpdate($sellId, $data);

        $Row = $this->mapToRow($data);
        $Row['sell_status'] = "SELECTING_Label";

        $this->repository->updateSellApi($sellId, $Row, $user_id);
    }
    public function updateSellSelectingApi(int $sellId, array $data, $user_id): void
    {
        $this->validator->validateSellUpdate($sellId, $data);
        $Row = $this->mapToRow($data);
        $Row['sell_status'] = "SELECTING_CPO";

        $this->repository->updateSellApi($sellId, $Row, $user_id);
    }
    public function updateSellSelectedLabelApi(int $sellId, array $data, $user_id): void
    {
        $this->validator->validateSellUpdate($sellId, $data);
        $Row = $this->mapToRow($data);
        $Row['sell_status'] = "SELECTED_LABEL";

        $this->repository->updateSellApi($sellId, $Row, $user_id);
    }
    public function updateSellStatusSelectingCpo(int $productId, array $data): void
    {
        $this->validator->validateSellUpdate($productId, $data);

        $Row = $this->mapToRow($data);
        $Row['sell_status']="SELECTING_CPO";
        $Row['total_qty']=$data['total_qty'];
        $user_id=$this->session->get('user')["id"];

        $this->repository->updateSellApi($productId, $Row, $user_id);

    }

    public function deleteSellApi(int $productId, array $data): void
    {
        $this->repository->deleteSell($productId);
    }


    private function mapToRow(array $data): array
    {
        $result = [];

        if (isset($data['sell_no'])) {
            $result['sell_no'] = (string)$data['sell_no'];
        }
        if (isset($data['sell_date'])) {
            $result['sell_date'] = (string)$data['sell_date'];
        }
        if (isset($data['product_id'])) {
            $result['product_id'] = (int)$data['product_id'];
        }
        if (isset($data['total_qty'])) {
            $result['total_qty'] = (int)$data['total_qty'];
        }
        if (isset($data['sell_status'])) {
            $result['sell_status'] = (string)$data['sell_status'];
        }

        return $result;
    }
}
