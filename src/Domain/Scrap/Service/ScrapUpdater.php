<?php

namespace App\Domain\Scrap\Service;

use App\Domain\Scrap\Repository\ScrapRepository;

/**
 * Service.
 */
final class ScrapUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        ScrapRepository $repository,
        ScrapValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function insertScrap(array $data): int
    {
        $this->validator->validateScrapInsert($data);

        $row = $this->mapToScrapRow($data);

        $id = $this->repository->insertScrap($row);

        $data['scrap_no'] = "SC" . str_pad($id, 10, "0", STR_PAD_LEFT);
        $this->repository->updateScrap($id, $data);

        return $id;
    }

    public function updateScrap(int $scrapID, array $data): void
    {

        $this->validator->validateScrapUpdate($scrapID, $data);

        $row = $this->mapToScrapRow($data);

        $this->repository->updateScrap($scrapID, $row);
    }

    public function insertScrapApi(array $data, $user_id): int
    {
        $this->validator->validateScrapInsert($data);

        $row = $this->mapToScrapRow($data);
        // $row['scrap_date'] = date('Y-m-d');

        $id = $this->repository->insertScrapApi($row, $user_id);

        $row['scrap_no'] = "SC" . str_pad($id, 10, "0", STR_PAD_LEFT);
        $this->repository->updateScrapApi($id, $row, $user_id);

        return $id;
    }
    public function updateScrapApi(int $id, array $data, $user_id): void
    {
        $this->validator->validateScrapUpdate($id, $data);

        $row = $this->mapToScrapRow($data);

        $this->repository->updateScrapApi($id, $row, $user_id);
    }

    public function deleteScrapApi(int $id): void
    {
        $this->repository->deleteScrap($id);
    }

    public function deleteScrap(int $id): void
    {
        $this->repository->deleteScrap($id);
    }

    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToScrapRow(array $data): array
    {
        $result = [];

        if (isset($data['scrap_no'])) {
            $result['scrap_no'] = (string)$data['scrap_no'];
        }
        if (isset($data['scrap_date'])) {
            $result['scrap_date'] = (string)$data['scrap_date'];
        }
        if (isset($data['scrap_status'])) {
            $result['scrap_status'] = (string)$data['scrap_status'];
        }
        if (isset($data['is_delete'])) {
            $result['is_delete'] = (string)$data['is_delete'];
        }

        return $result;
    }
}
