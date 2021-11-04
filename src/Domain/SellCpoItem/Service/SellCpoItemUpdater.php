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
        Session $session,
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
    public function updateSellCpoItemApi(int $productId, array $data): void
    {
        // Input validation
        $this->validator->validateSellCpoItemUpdate($productId, $data);

        // Map form data to row
        $storeRow = $this->mapToRow($data);

        // Insert store
        $this->repository->updateSellCpoItem($productId, $storeRow);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }
    public function deleteSellCpoItemApi(int $productId, array $data): void
    {

        // Insert store
        $this->repository->deleteSellCpoItem($productId);

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

    
    private function mapToRow(array $data): array
    {
        $result = [];

        if (isset($data['sell_id'])) {
            $result['sell_id'] = (int)$data['sell_id'];
        }
        if (isset($data['CpoItemID'])) {
            $result['cpo_item_id'] = (int)$data['CpoItemID'];
        }
        if (isset($data['remain_qty'])) {
            $result['remain_qty'] = (int)$data['remain_qty'];
        }
        if (isset($data['sell_qty'])) {
            $result['sell_qty'] = (int)$data['sell_qty'];
        }

        return $result;
    }
}
