<?php

namespace App\Domain\LotDefect\Service;

use App\Domain\LotDefect\Repository\LotDefectRepository;

/**
 * Service.
 */
final class LotDefectUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        LotDefectRepository $repository,
        LotDefectValidator $validator,
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

<<<<<<< HEAD
    public function insertLotDefectApi( array $data, $user_id): int
=======
    public function insertLotDefect(array $data, $user_id): int
>>>>>>> tae
    {
        // Input validation
        $this->validator->validateLotDefectInsert($data);

        // Map form data to row
<<<<<<< HEAD
        $lotDefectRow = $this->mapToLotDefectRow($data);

        // Insert transferStore
        $id=$this->repository->insertLotDefectApi($lotDefectRow, $user_id);
=======
        $Row = $this->mapToLotDefectRow($data);

        // Insert transferStore
        $id=$this->repository->insertLotDefect($Row, $user_id);
>>>>>>> tae

        // Logging
        //$this->logger->info(sprintf('TransferStore updated successfully: %s', $id));
        return $id;
    }
<<<<<<< HEAD
    public function updateLotDefectApi(int $lotDefect, $user_id ,array $data): void
    {
        // Input validation
        $this->validator->validateLotDefectUpdate($lotDefect, $data);
=======
    
    public function updateLotDefect(int $lotDefectId, array $data, $user_id): void
    {
        // Input validation
        $this->validator->validateLotDefectUpdate($data);
>>>>>>> tae

        // Map form data to row
        $storeRow = $this->mapToLotDefectRow($data);

        // Insert store
<<<<<<< HEAD
        $this->repository->updateLotDefectApi($lotDefect, $storeRow,$user_id);
=======
        $this->repository->updateLotDefect($lotDefectId, $storeRow, $user_id);
>>>>>>> tae

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }
<<<<<<< HEAD
    

    public function deleteLotDefect(int $lotDefect): void
    {

        // Insert store
        $this->repository->deleteLotDefect($lotDefect);
=======

    public function deleteLotDefect(int $lotDefectId, array $data): void
    {

        // Insert store
        $this->repository->deleteLotDefect($lotDefectId, $data);
>>>>>>> tae

        // Logging
        //$this->logger->info(sprintf('Store updated successfully: %s', $storeId));
    }

<<<<<<< HEAD
    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
=======
>>>>>>> tae
    private function mapToLotDefectRow(array $data): array
    {
        $result = [];

        if (isset($data['lot_id'])) {
            $result['lot_id'] = (string)$data['lot_id'];
        }
        if (isset($data['defect_id'])) {
            $result['defect_id'] = (string)$data['defect_id'];
        }
        if (isset($data['quantity'])) {
            $result['quantity'] = (string)$data['quantity'];
        }

<<<<<<< HEAD
=======

>>>>>>> tae
        return $result;
    }
}
