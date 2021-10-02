<?php

namespace App\Domain\SellCpoItem\Service;

use App\Domain\SellCpoItem\Repository\SellCpoItemRepository;

/**
 * Service.
 */
final class SellCpoItemUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        SellCpoItemRepository $repository,
        SellCpoItemValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertSellCpoItemApi(array $data, $user_id): int
    {
        // Input validation
        $this->validator->validateSellCpoItemInsert($data);

        // Map form data to row
        $Row = $this->mapToRow($data);

        // Insert transferStore
        $id=$this->repository->insertSellCpoItemApi($Row, $user_id);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
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
        if (isset($data['cpo_item_id'])) {
            $result['cpo_item_id'] = (int)$data['cpo_item_id'];
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
