<?php

namespace App\Domain\SellCpoItem\Service;

use App\Domain\SellCpoItem\Repository\SellCpoItemRepository;
use Symfony\Component\HttpFoundation\Session\Session;
/**
 * Service.
 */
final class SellCpoItemUpdater
{
    private $repository;
    private $validator;
    private $session;

    public function __construct(
        SellCpoItemRepository $repository,
        SellCpoItemValidator $validator,
        Session $session
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->session=$session;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertSellCpoItemApi(array $data, $user_id): int
    {
        $this->validator->validateSellCpoItemInsert($data);

        $row = $this->mapToRow($data);

        $id=$this->repository->insertSellCpoItemApi($row, $user_id);

        return $id;
    }
    public function insertSellCpoItem(array $data): int
    {
        $this->validator->validateSellCpoItemInsert($data);

        $row = $this->mapToRow($data);
        $user_id=$this->session->get('user')["id"];

        $id=$this->repository->insertSellCpoItemApi($row, $user_id);

        return $id;
    }
    public function updateSellCpoItemApi(int $id, array $data): void
    {
        $this->validator->validateSellCpoItemUpdate($id, $data);

        $row = $this->mapToRow($data);

        $this->repository->updateSellCpoItem($id, $row);

    }
    public function deleteSellCpoItemApi(int $id): void
    {
        $this->repository->deleteSellCpoItem($id);
    }
    public function deleteCpoItemInSellCpoItemApi(int $sellID): void
    {
        $this->repository->deleteCpoItemInSellCpoItemApi($sellID);
    }

    private function mapToRow(array $data): array
    {
        $result = [];

        if (isset($data['sell_id'])) {
            $result['sell_id'] = $data['sell_id'];
        }
        if (isset($data['cpo_item_id'])) {
            $result['cpo_item_id'] = $data['cpo_item_id'];
        }
        if (isset($data['remain_qty'])) {
            $result['remain_qty'] = $data['remain_qty'];
        }
        if (isset($data['sell_qty'])) {
            $result['sell_qty'] = $data['sell_qty'];
        }

        return $result;
    }
}
