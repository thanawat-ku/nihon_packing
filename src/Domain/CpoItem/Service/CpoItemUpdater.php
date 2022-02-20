<?php

namespace App\Domain\CpoItem\Service;

use App\Domain\CpoItem\Repository\CpoItemRepository;

use function DI\string;

/**
 * Service.
 */
final class CpoItemUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        CpoItemRepository $repository,
        CpoItemValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function updateCpoItem(int $id, array $data): void
    {
        $this->validator->validateCpoItemUpdate($id, $data);

        $row = $this->mapToRow($data);

        $this->repository->updateCpoItem($id, $row);
    }
    
    public function deleteCpoItem(int $id): void
    {
        $this->repository->deleteCpoItem($id);
    }

    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToRow(array $data): array
    {
        $result = [];

        if (isset($data['PackingQty'])) {
            $result['PackingQty'] = $data['PackingQty'];
        }

        return $result;
    }
}
