<?php

namespace App\Domain\Section\Service;

use App\Domain\Section\Repository\SectionRepository;

/**
 * Service.
 */
final class SectionUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        SectionRepository $repository,
        SectionValidator $validator,
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }
    public function insertSection( array $data): int
    {
        // Input validation
        $this->validator->validateSectionInsert($data);

        // Map form data to row
        $productRow = $this->mapToSectionRow($data);

        // Insert transferStore
        $id=$this->repository->insertSection($productRow);

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
    private function mapToSectionRow(array $data): array
    {
        $result = [];

        if (isset($data['id'])) {
            $result['id'] = (string)$data['id'];
        }
        if (isset($data['section_name'])) {
            $result['section_name'] = (string)$data['section_name'];
        }
        if (isset($data['section_description'])) {
            $result['section_description'] = (string)$data['section_description'];
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
