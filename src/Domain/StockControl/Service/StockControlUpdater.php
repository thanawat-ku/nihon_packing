<?php

namespace App\Domain\StockControl\Service;

use App\Domain\StockControl\Repository\StockControlRepository;

use function DI\string;

/**
 * Service.
 */
final class StockControlUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        StockControlRepository $repository,
        StockControlValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function insertStockControl(array $data): int
    {
        $this->validator->validateStockControlInsert($data);

        $row = $this->mapToRow($data);

        $id = $this->repository->insertStockControl($row);

        return $id;
    }

    public function insertStockControlApi(array $data, $user_id): int
    {
        $this->validator->validateStockControlInsert($data);

        $row = $this->mapToRow($data);

        $id = $this->repository->insertStockControl($row, $user_id);

        return $id;
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


        if (isset($data['PackingNo'])) {
            $result['DocNo'] = $data['PackingNo'];
        }

        return $result;
    }
}
