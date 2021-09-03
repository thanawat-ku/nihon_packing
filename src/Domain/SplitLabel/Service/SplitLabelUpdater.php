<?php

namespace App\Domain\SplitLabel\Service;

use App\Domain\SplitLabel\Repository\SplitLabelRepository;
use App\Domain\SplitLabel\Service\SplitLabelFinder;

/**
 * Service.
 */
final class SplitLabelUpdater
{
    private $repository;
    private $validator;
    private $finder;

    public function __construct(
        SplitLabelRepository $repository,
        SplitLabelValidator $validator,
        SplitLabelFinder $finder
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->finder = $finder;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }


    public function insertSplitLabelApi(array $data, $user_id): int
    {
        $this->validator->validateSplitLabelInsert($data);

        $Row = $this->mapToSplitLabelRow($data);

        $id = $this->repository->insertSplitLabelApi($Row, $user_id);

        return $id;
    }
    public function insertSplitLabelDeatilApi(array $data, $user_id): int
    {
        $this->validator->validateSplitLabelInsert($data);

        $Row = $this->mapToSplitLabelDeatilRow($data);

        $id = $this->repository->insertSplitLabelDeatilApi($Row, $user_id);

        return $id;
    }

    public function updateSplitLabel(int $labelID, array $data): void
    {
        // Input validation
        $this->validator->validateSplitLabelUpdate($labelID, $data);


        $storeRow = $this->mapToSplitLabelRow($data);

        // Insert store
        $this->repository->updateSplitLabel($labelID, $storeRow);
    }

    public function updateSplitLabelApi(array $data, int $splitID, $user_id): void
    {
        // Input validation
        $this->validator->validateSplitLabelUpdate($splitID, $data);

        // Map form data to row
        $storeRow = $this->mapToSplitLabelRow($data);

        // Insert store
        $this->repository->updateSplitLabelApi($splitID, $storeRow, $user_id);
    }

    public function registerSplitLabelApi(array $data, int $splitID, $user_id): void
    {
        // Input validation
        $this->validator->validateSplitLabelUpdate($splitID, $data);

        // Map form data to row
        $storeRow = $this->mapToSplitLabelRow($data);

        // Insert store
        $this->repository->updateSplitLabelApi($splitID, $storeRow, $user_id);
    }



    public function deleteSplitLabel(int $labelID, array $data): void
    {
        $this->repository->deleteSplitLabel($labelID);
    }


    private function mapToSplitLabelRow(array $data): array
    {
        $result = [];

        if (isset($data['split_label_no'])) {
            $result['split_label_no'] = (string)$data['split_label_no'];
        }
        if (isset($data['label_id'])) {
            $result['label_id'] = (string)$data['label_id'];
        }
        if (isset($data['status'])) {
            $result['status'] = (string)$data['status'];
        }

        return $result;
    }

    private function mapToSplitLabelDeatilRow(array $data): array
    {
        $result = [];

        if (isset($data['split_label_id'])) {
            $result['split_label_id'] = (string)$data['split_label_id'];
        }
        if (isset($data['label_id'])) {
            $result['label_id'] = (string)$data['label_id'];
        }

        return $result;
    }
}
