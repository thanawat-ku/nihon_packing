<?php

namespace App\Domain\ReportAll\Service;

use App\Domain\ReportAll\Repository\ReportAllRepository;

/**
 * Service.
 */
final class ReportAllUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        ReportAllRepository $repository,
        ReportAllValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }
    // public function insertReportAll( array $data): int
    // {
    //     // Input validation
    //     $this->validator->validateReportAllInsert($data);

    //     // Map form data to row
    //     $productRow = $this->mapToReportAllRow($data);

    //     // Insert transferStore
    //     $id=$this->repository->insertReportAll($productRow);

    //     // Logging
    //     //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
    //     return $id;
    // }
    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToReportAllRow(array $data): array
    {
        $result = [];

        if (isset($data['id'])) {
            $result['id'] = (string)$data['id'];
        }
        if (isset($data['ReportAll_name'])) {
            $result['ReportAll_name'] = (string)$data['ReportAll_name'];
        }
        if (isset($data['ReportAll_description'])) {
            $result['ReportAll_description'] = (string)$data['ReportAll_description'];
        }
        if (isset($data['is_vendor'])) {
            $result['is_vendor'] = (string)$data['is_vendor'];
        }
        if (isset($data['is_scrap'])) {
            $result['is_scrap'] = (string)$data['is_scrap'];
        }
        
        return $result;
    }
}
