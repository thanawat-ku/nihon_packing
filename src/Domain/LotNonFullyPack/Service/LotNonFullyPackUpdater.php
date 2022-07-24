<?php

namespace App\Domain\LotNonFullyPack\Service;


use App\Domain\LotNonFullyPack\Repository\LotNonFullyPackRepository;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Service.
 */
final class LotNonFullyPackUpdater
{
    private $repository;
    private $validator;
    private $session;

    public function __construct(
        Session $session,
        LotNonFullyPackRepository $repository,
        LotNonFullyPackValidator $validator,
        
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->session = $session;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function insertLotNonFullyPack(array $data): int
    {
        $this->validator->validateLotNonFullyPackInsert($data);

        $row = $this->mapToRow($data);

        $user_id=$row['updated_user_id'] = $this->session->get('user')["id"];

        $id = $this->repository->insertLotNonFullyPack($data, $user_id);

        return $id;
    }    

  

    // public function deleteLotNonFullyPack(int $labelId, array $data): void
    // {
    //     $this->repository->deleteLotNonFullyPack($labelId);
    // }

    public function deleteLotNonFullyPack(int $labelId): void
    {
        $this->repository->deleteLotNonFullyPack($labelId);
    }

    // public function updatePackMerge(string $mergeId, array $data): void
    // {

    //     $this->validator->validateLotNonFullyPackUpdate($mergeId, $data);

    //     $storeRow = $this->mapToLotNonFullyPackRow($data);

    //     $this->repository->updatePackMerge($mergeId, $storeRow);
    // }

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
        
        if (isset($data['lot_id'])) {
            $result['lot_id'] = $data['lot_id'];
        }
        if (isset($data['label_id'])) {
            $result['label_id'] = $data['label_id'];
        }
        if (isset($data['date'])) {
            $result['date'] = $data['date'];
        }
        if (isset($data['is_register'])) {
            $result['is_register'] = $data['is_register'];
        }


        return $result;
    }
}
