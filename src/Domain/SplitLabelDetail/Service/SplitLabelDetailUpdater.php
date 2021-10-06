<?php

namespace App\Domain\SplitLabelDetail\Service;

use App\Domain\SplitLabelDetail\Repository\SplitLabelDetailRepository;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;

/**
 * Service.
 */
final class SplitLabelDetailUpdater
{
    private $repository;
    private $validator;
    private $finder;

    public function __construct(
        SplitLabelDetailRepository $repository,
        SplitLabelDetailValidator $validator,
        SplitLabelDetailFinder $finder
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->finder = $finder;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }


    
    public function insertSplitLabelDetailDeatilApi(array $data, $user_id): int
    {
        $this->validator->validateSplitLabelDetailInsert($data);

        $Row = $this->mapToSplitLabelDetailDeatilRow($data);

        $id = $this->repository->insertSplitLabelDetailDeatilApi($Row, $user_id);

        return $id;
    }

    public function deleteSplitLabelDetail(int $labelID, array $data): void
    {
        $this->repository->deleteSplitLabelDetail($labelID);
    }

    public function deleteSplitLabelDetailAll(int $labelID, array $data): void
    {
        $this->repository->deleteSplitLabelDetailAll($labelID);
    }



    

    private function mapToSplitLabelDetailDeatilRow(array $data): array
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
