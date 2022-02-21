<?php

namespace App\Domain\Packing\Service;

use App\Domain\Packing\Repository\PackingRepository;

use function DI\string;

/**
 * Service.
 */
final class PackingUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        PackingRepository $repository,
        PackingValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }
    public function insertPacking( array $data): int
    {
        $this->validator->validatePackingInsert($data);

        $row = $this->mapToRow($data);

        $id=$this->repository->insertPacking($row);

        return $id;
    }
    public function insertPackingApi(array $data, $user_id): int
    {
        $this->validator->validatePackingInsert($data);

        $row = $this->mapToRow($data);

        $id=$this->repository->insertPackingApi($row, $user_id);

        return $id;
    }

    // public function updatePacking(int $id, array $data): void
    // {
    //     $this->validator->validatePackingUpdate($id, $data);

    //     $row = $this->mapToRow($data);

    //     $this->repository->updatePacking($id, $row);
    // }
    
    // public function deletePacking(int $id): void
    // {
    //     $this->repository->deletePacking($id);
    // }

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

        if (isset($data['PackingID'])) {
            $result['PackingID'] = $data['PackingID'];
        }
        if (isset($data['PackingNo'])) {
            $result['PackingNo'] = $data['PackingNo'];
        }
        if (isset($data['PackingNum'])) {
            $result['PackingNum'] = $data['PackingNum'];
        }
        if (isset($data['IssueDate'])) {
            $result['IssueDate'] = $data['IssueDate'];
        }
        if (isset($data['UpdateTime'])) {
            $result['UpdateTime'] = $data['UpdateTime'];
        }
        if (isset($data['UserID'])) {
            $result['UserID'] = $data['UserID'];
        }

        return $result;
    }
}
