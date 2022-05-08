<?php

namespace App\Domain\PackingItem\Service;

use App\Domain\PackingItem\Repository\PackingItemRepository;

use function DI\string;

/**
 * Service.
 */
final class PackingItemUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        PackingItemRepository $repository,
        PackingItemValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }
    public function insertPackingItem(array $data): int
    {
        $this->validator->validatePackingItemInsert($data);

        $row = $this->mapToRow($data);

        $id=$this->repository->insertPackingItem($row);

        return $id;
    }
    public function updatePackingItem(int $packingID, $lotID, array $data): void
    {
        $this->validator->validatePackingItemUpdate($packingID, $lotID, $data);

        $row = $this->mapToRow($data);

        $this->repository->updatePackingItem($packingID, $lotID, $row);
    }

    public function deletePackingItemAll(int $id): void
    {
        $this->repository->deletePackingItemAll($id);
    }

    private function mapToRow(array $data): array
    {
        $result = [];

        if (isset($data['PackingID'])) {
            $result['PackingID'] = $data['PackingID'];
        }
        if (isset($data['LotID'])) {
            $result['LotID'] = $data['LotID'];
        }
        if (isset($data['CpoItemID'])) {
            $result['CpoItemID'] = $data['CpoItemID'];
        }
        if (isset($data['Quantity'])) {
            $result['Quantity'] = $data['Quantity'];
        }
        if (isset($data['InvoiceItemID'])) {
            $result['InvoiceItemID'] = $data['InvoiceItemID'];
        }

        return $result;
    }
}
