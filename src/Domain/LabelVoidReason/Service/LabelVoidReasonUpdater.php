<?php

namespace App\Domain\LabelVoidReason\Service;

use App\Domain\LabelVoidReason\Repository\LabelVoidReasonRepository;

/**
 * Service.
 */
final class LabelVoidReasonUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        LabelVoidReasonRepository $repository,
        LabelVoidReasonValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function insertLabelVoidReason(array $data): int
    {
        $this->validator->validateLabelVoidReasonInsert($data);

        $row = $this->mapToLabelVoidReasonRow($data);

        $id = $this->repository->insertLabelVoidReason($row);

        return $id;
    }
    public function insertLabelVoidReasonApi(array $data, $user_id): int
    {
        $this->validator->validateLabelVoidReasonInsert($data);

        $row = $this->mapToLabelVoidReasonRow($data);

        $id = $this->repository->insertLabelVoidReasonApi($row, $user_id);

        return $id;
    }
    public function updateLabelVoidReason(int $voidReasonId, array $data): void
    {
        $this->validator->validateLabelVoidReasonUpdate($voidReasonId, $data);

        $row = $this->mapToLabelVoidReasonRow($data);

        $this->repository->updateLabelVoidReason($voidReasonId, $row);
    }
    public function updateLabelVoidReasonApi(int $voidReasonId, array $data, $user_id): void
    {
        $this->validator->validateLabelVoidReasonUpdate($voidReasonId, $data);

        $row = $this->mapToLabelVoidReasonRow($data);

        $this->repository->updateLabelVoidReasonApi($voidReasonId, $row, $user_id);
    }

    public function deleteLabelVoidReason(int $voidReasonId): void
    {
        $this->repository->deleteLabelVoidReason($voidReasonId);
    }

    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToLabelVoidReasonRow(array $data): array
    {
        $result = [];

        if (isset($data['reason_name'])) {
            $result['reason_name'] = (string)$data['reason_name'];
        }
        if (isset($data['description'])) {
            $result['description'] = (string)$data['description'];
        }

        return $result;
    }
}
