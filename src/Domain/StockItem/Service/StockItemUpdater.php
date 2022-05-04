<?php

namespace App\Domain\StockItem\Service;

use App\Domain\StockItem\Repository\StockItemRepository;

use function DI\string;

/**
 * Service.
 */
final class StockItemUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        StockItemRepository $repository,
        StockItemValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function insertStockItem(array $data): int
    {
        $this->validator->validateStockItemInsert($data);

        $row = $this->mapToRow($data);

        $id = $this->repository->insertStockItem($row);

        return $id;
    }

    public function updateStockItem(int $id, array $data): void
    {
        $this->validator->validateStockItemUpdate($id, $data);

        $row = $this->mapToRow($data);

        $this->repository->updateStockItem($id, $row);
    }


    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToRow(array $data): array
    {
        $result = [];

        if (isset($data['StockControlID'])) {
            $result['StockControlID'] = $data['StockControlID'];
        }
        if (isset($data['OutStockControlID'])) {
            $result['OutStockControlID'] = $data['OutStockControlID'];
        }
        if (isset($data['LotID'])) {
            $result['LotID'] = $data['LotID'];
        }
        if (isset($data['Quantity'])) {
            $result['Quantity'] = $data['Quantity'];
        }

        return $result;
    }
}
