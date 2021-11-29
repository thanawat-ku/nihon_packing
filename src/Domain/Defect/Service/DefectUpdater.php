<?php

namespace App\Domain\Defect\Service;

use App\Domain\Defect\Repository\DefectRepository;

/**
 * Service.
 */
final class DefectUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        DefectRepository $repository,
        DefectValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }
    public function insertDefect( array $data): int
    {
        // Input validation
        $this->validator->validateDefectInsert($data);

        // Map form data to row
        $productRow = $this->mapToDefectRow($data);

        // Insert transferStore
        $id=$this->repository->insertDefect($productRow);

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToDefectRow(array $data): array
    {
        $result = [];

        if (isset($data['id'])) {
            $result['id'] = (string)$data['id'];
        }
        if (isset($data['defect_code'])) {
            $result['defect_code'] = (string)$data['defect_code'];
        }
        if (isset($data['defect_description'])) {
            $result['defect_description'] = (string)$data['defect_description'];
        }
        if (isset($data['oqc_check'])) {
            $result['oqc_check'] = (string)$data['oqc_check'];
        }
        
        return $result;
    }
}
