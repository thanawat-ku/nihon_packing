<?php

namespace App\Domain\ScrapDetail\Service;

use App\Domain\ScrapDetail\Repository\ScrapDetailRepository;

/**
 * Service.
 */
final class ScrapDetailUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        ScrapDetailRepository $repository,
        ScrapDetailValidator $validator,
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function insertScrapDetail(array $data): int
    {
        $this->validator->validateScrapDetailInsert($data);

        $row = $this->mapToScrapDetailRow($data);

        $id = $this->repository->insertScrapDetail($row);

        return $id;
    }

    public function updateScrapDetail(int $lotDefectId, array $data): void
    {
        $this->validator->validateScrapDetailUpdate($lotDefectId, $data);

        $row = $this->mapToScrapDetailRow($data);

        $this->repository->updateScrapDetail($lotDefectId, $row);

    }

    public function insertScrapDetailApi(array $data, $user_id): int
    {
        $this->validator->validateScrapDetailInsert($data);

        $row = $this->mapToScrapDetailRow($data);

        $id = $this->repository->insertScrapDetailApi($row, $user_id);

        return $id;
    }
    public function updateScrapDetailApi(int $sdID, array $data, $user_id): void
    {
        $this->validator->validateScrapDetailUpdate($sdID, $data);

        $row = $this->mapToScrapDetailRow($data);

        $this->repository->updateScrapDetailApi($sdID, $row, $user_id);

    }

    public function deleteScrapDetail(int $id): void
    {
        $this->repository->deleteScrapDetail($id);
    }

    public function deleteScrapDetailAll(int $sdID): void
    {
        $this->repository->deleteScrapDetailAll($sdID);
    }
    

    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToScrapDetailRow(array $data): array
    {
        $result = [];

        if (isset($data['scrap_id'])) {
            $result['scrap_id'] = $data['scrap_id'];
        }
        if (isset($data['defect_id'])) {
            $result['defect_id'] = $data['defect_id'];
        }
        if (isset($data['product_id'])) {
            $result['product_id'] = $data['product_id'];
        }
        if (isset($data['section_id'])) {
            $result['section_id'] = $data['section_id'];
        }
        if (isset($data['quantity'])) {
            $result['quantity'] = $data['quantity'];
        }

        return $result;
    }
}
