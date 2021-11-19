<?php

namespace App\Domain\CpoItem\Service;

use App\Domain\CpoItem\Repository\CpoItemRepository;

use function DI\string;

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

    
    public function updateCpoItem(int $id, array $data): void
    {
        $this->validator->validateCpoItemUpdate($id, $data);

        $storeRow = $this->mapToCpoItemRow($data);

        $this->repository->updateCpoItem($id, $storeRow);
    }

    
    public function deleteCpoItem(int $id): void
    {
        $this->repository->deleteCpoItem($id);
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

        if (isset($data['sell_id'])) {
            $result['sell_id'] = (string)$data['sell_id'];
        }
        if (isset($data['cpo_item_id'])) {
            $result['cpo_item_id'] = (string)$data['cpo_item_id'];
        }
        if (isset($data['remain_qty'])) {
            $result['remain_qty'] = (string)$data['remain_qty'];
        }
        if (isset($data['sell_qty'])) {
            $result['sell_qty'] = (string)$data['sell_qty'];
        }

        return $result;
    }
}
