<?php

namespace App\Domain\SplitLabel\Service;

use App\Domain\SplitLabel\Repository\SplitLabelRepository;

/**
 * Service.
 */
final class SplitLabelUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        SplitLabelRepository $repository,
        SplitLabelValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertSplitLabel( array $data): int
    {
        // Input validation
        $this->validator->validateSplitLabelInsert($data);

        // Map form data to row
        $splitLabelRow = $this->mapToSplitLabelRow($data);

        // Insert transferStore
        $id=$this->repository->insertSplitLabel($splitLabelRow);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }

    public function insertSplitLabelApi( array $data,$user_id ): int
    {

        $this->validator->validateSplitLabelInsert($data);


        $splitLabelRow = $this->mapToSplitLabelRow($data);


        //$id=$this->repository->insertSplitLabelApi($splitLabelRow,$user_id );
        return 0;
        //return $id;
    }

    public function updateSplitLabel(int $splitLabelId, array $data): void
    {

        $this->validator->validateSplitLabelUpdate($splitLabelId, $data);

  
        $storeRow = $this->mapToSplitLabelRow($data);

        $this->repository->updateSplitLabel($splitLabelId, $storeRow);

    }

    public function deleteSplitLabel(int $splitLabelId, array $data): void
    {
        // Insert store
        $this->repository->deleteSplitLabel($splitLabelId);
    }

    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
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
