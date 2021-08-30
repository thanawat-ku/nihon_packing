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

    public function insertSplitLabel(array $data): int
    {
        // Input validation
        $this->validator->validateSplitLabelInsert($data);

        // Map form data to row
        $lotRow = $this->mapToSplitLabelRow($data);

        // Insert transferStore
        $id = $this->repository->insertSplitLabel($lotRow);

        return $id;
    }

    public function insertSplitLabelApi(array $data, $user_id): int
    {
        $this->validator->validateSplitLabelInsert($data);

        $Row = $this->mapToSplitLabelRow($data);

        $id = $this->repository->insertSplitLabelApi($Row, $user_id);

        return $id;
    }



    public function updateSplitLabel(int $labelID, array $data): void
    {
        // Input validation
        $this->validator->validateSplitLabelUpdate($labelID, $data);

        // Map form data to row
        $storeRow = $this->mapToSplitLabelRow($data);

        // Insert store
        $this->repository->updateSplitLabel($labelID, $storeRow);
    }

    public function updateInsertSplitLabelApi(array $data,int $splitID,$user_id ): void
    {
        // Input validation
        $this->validator->validateSplitLabelUpdate($splitID, $data);

        // Map form data to row
        $storeRow = $this->mapToSplitLabelRow($data);

        // Insert store
        $this->repository->updateSplitLabelApi($splitID, $storeRow,$user_id );
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
}
