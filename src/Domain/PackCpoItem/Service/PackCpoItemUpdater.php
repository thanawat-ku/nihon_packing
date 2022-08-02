<?php

namespace App\Domain\PackCpoItem\Service;

use App\Domain\PackCpoItem\Repository\PackCpoItemRepository;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Service.
 */
final class PackCpoItemUpdater
{
    private $repository;
    private $validator;
    private $session;

    public function __construct(
        PackCpoItemRepository $repository,
        PackCpoItemValidator $validator,
        Session $session
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->session = $session;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function insertPackCpoItemApi(array $data, $user_id): int
    {
        $this->validator->validatePackCpoItemInsert($data);

        $row = $this->mapToRow($data);

        $id = $this->repository->insertPackCpoItemApi($row, $user_id);

        return $id;
    }
    public function insertPackCpoItem(array $data): int
    {
        $this->validator->validatePackCpoItemInsert($data);

        $row = $this->mapToRow($data);
        $user_id = $this->session->get('user')["id"];

        $id = $this->repository->insertPackCpoItemApi($row, $user_id);

        return $id;
    }
    public function updatePackCpoItemApi(int $id, array $data,  $user_id): void
    {
        $this->validator->validatePackCpoItemUpdate($id, $data);

        $row = $this->mapToRow($data);

        $this->repository->updatePackCpoItemApi($id, $row,  $user_id);
    }
    public function updatePackCpoItem(int $id, array $data): void
    {
        $this->validator->validatePackCpoItemUpdate($id, $data);

        $row = $this->mapToRow($data);

        $this->repository->updatePackCpoItem($id, $row);
    }
    public function deletePackCpoItemApi(int $id): void
    {
        $this->repository->deletePackCpoItem($id);
    }
    public function deleteCpoItemInPackCpoItemApi(int $packID): void
    {
        $this->repository->deleteCpoItemInPackCpoItemApi($packID);
    }

    private function mapToRow(array $data): array
    {
        $result = [];

        if (isset($data['pack_id'])) {
            $result['pack_id'] = $data['pack_id'];
        }
        if (isset($data['cpo_item_id'])) {
            $result['cpo_item_id'] = $data['cpo_item_id'];
        }
        if (isset($data['remain_qty'])) {
            $result['remain_qty'] = $data['remain_qty'];
        }
        if (isset($data['pack_qty'])) {
            $result['pack_qty'] = $data['pack_qty'];
        }
        if (isset($data['due_date'])) {
            $result['due_date'] = $data['due_date'];
        }

        return $result;
    }
}
