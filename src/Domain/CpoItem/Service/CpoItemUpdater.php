<?php

namespace App\Domain\CpoItem\Service;

use App\Domain\CpoItem\Repository\CpoItemRepository;

/**
 * Service.
 */
final class CpoItemUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        CpoItemRepository $repository,
        CpoItemValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    // public function insertCpoItem( array $data): int
    // {
    //     // Input validation
    //     $this->validator->validateCpoItemInsert($data);

    //     // Map form data to row
    //     $cpo_itemRow = $this->mapToCpoItemRow($data);

    //     // Insert transferStore
    //     $id=$this->repository->insertCpoItem($cpo_itemRow);

    //     // Logging
    //     //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
    //     return $id;
    // }
    public function updateCpoItem(int $cpo_item, array $data): void
    {
        $this->validator->validateCpoItemUpdate($cpo_item, $data);

        $storeRow = $this->mapToCpoItemRow($data);

        $this->repository->updateCpoItem($cpo_item, $storeRow);
    }
    public function deleteCpoItem(int $cpoItemID): void
    {
        $this->repository->deleteCpoItem($cpoItemID);
    }

    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToCpoItemRow(array $data): array
    {
        $result = [];

        if (isset($data['part_code'])) {
            $result['part_code'] = (string)$data['part_code'];
        }
        if (isset($data['part_name'])) {
            $result['part_name'] = (string)$data['part_name'];
        }
        if (isset($data['std_pack'])) {
            $result['std_pack'] = (string)$data['std_pack'];
        }
        if (isset($data['std_box'])) {
            $result['std_box'] = (string)$data['std_box'];
        }

        return $result;
    }
}
