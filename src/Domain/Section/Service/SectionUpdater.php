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

        if (isset($data['section_name'])) {
            $result['section_name'] = (string)$data['section_name'];
        }
        if (isset($data['section_description'])) {
            $result['section_description'] = (string)$data['section_description'];
        }
        
        return $result;
    }
}
