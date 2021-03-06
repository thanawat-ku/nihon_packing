<?php

namespace App\Domain\MergePack\Service;

use App\Domain\MergePack\Repository\MergePackRepository;

/**
 * Service.
 */
final class MergePackUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        MergePackRepository $repository,
        MergePackValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function insertMergePackApi(array $data, $user_id): int
    {
        $this->validator->validateMergePackInsert($data);

        $row = $this->mapToMergePackRow($data);
        $row['merge_date'] = date('Y-m-d');
        $row['merge_no'] = "X" . str_pad(0, 11, "0", STR_PAD_LEFT);
        $id = $this->repository->insertMergePackApi($row, $user_id);

        $data1['merge_no'] = "M" . str_pad($id, 11, "0", STR_PAD_LEFT);

        $this->repository->updateMergePackApi($id, $data1, $user_id);

        return $id;
    }

    public function insertMergePackFromLabel(array $data, $user_id): int
    {
        $this->validator->validateMergePackInsert($data);

        $row = $this->mapToMergePackRow($data);
        $row['merge_date'] = date('Y-m-d');
        $row['merge_no'] = "X" . str_pad(0, 11, "0", STR_PAD_LEFT);
        $id = $this->repository->insertMergePackFromLabel($row, $user_id);

        $data1['merge_no'] = "M" . str_pad($id, 11, "0", STR_PAD_LEFT);

        $this->repository->updateMergePackApi($id, $data1, $user_id);

        return $id;
    }

    public function updateMergePackApi(int $id, array $data, $user_id): void
    {
        $this->validator->validateMergePackUpdate($id, $data);

        $row = $this->mapToMergePackRow($data);

        $this->repository->updateMergePackApi($id, $row, $user_id);
    }

    public function updateMergingApi(int $id, array $data, $user_id): void
    {
        $this->validator->validateMergePackUpdate($id, $data);

        $row = $this->mapToMergePackRow($data);
        $row['merge_status'] = "MERGING";

        $this->repository->updateMergingApi($id, $row, $user_id);
    }

    public function updateStatusPrintedApi(int $id, array $data, $user_id): void
    {

        $this->validator->validateMergePackUpdate($id, $data);

        $row = $this->mapToMergePackRow($data);
        $row['merge_status'] = "PRINTED";

        $this->repository->updateMergingApi($id, $row, $user_id);
    }

    public function updateLabelPackMergeApi(string $labelNo, array $data, $user_id): void
    {
        $this->validator->validateMergePackUpdate($labelNo, $data);

        $row = $this->mapToMergePackRow($data);

        $this->repository->updateLabelPackMergeApi($labelNo, $row, $user_id);
    }

    public function deleteMergePackApi(int $labelId, array $data): void
    {
        $this->repository->deleteMergePackApi($labelId);
    }

    public function deleteMergePack(int $mergeId): void
    {
        $this->repository->deleteMergePack($mergeId);
    }

    public function updatePackMerge(string $mergeId, array $data): void
    {

        $this->validator->validateMergePackUpdate($mergeId, $data);

        $storeRow = $this->mapToMergePackRow($data);

        $this->repository->updatePackMerge($mergeId, $storeRow);
    }



    // public function deleteMergePack(int $id): void
    // {

    //     // Insert store
    //     $this->repository->deleteMergePack($id);

    //     // Logging
    //     //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    // }

    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToMergePackRow(array $data): array
    {
        $result = [];

        if (isset($data['product_id'])) {
            $result['product_id'] = $data['product_id'];
        }
        if (isset($data['merge_no'])) {
            $result['merge_no'] = $data['merge_no'];
        }
        if (isset($data['merge_status'])) {
            $result['merge_status'] = $data['merge_status'];
        }
        if (isset($data['is_delete'])) {
            $result['is_delete'] = $data['is_delete'];
        }


        return $result;
    }
}
