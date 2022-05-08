<?php

namespace App\Domain\PackLabel\Service;

use App\Domain\PackLabel\Repository\PackLabelRepository;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Service.
 */
final class PackLabelUpdater
{
    private $repository;
    private $validator;
    private $session;

    public function __construct(
        PackLabelRepository $repository,
        PackLabelValidator $validator,
        Session $session
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->session = $session;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function insertPackLabelApi(array $data, $user_id): int
    {
        $this->validator->validatePackLabelInsert($data);

        $row = $this->mapToRow($data);
        // $row['label_id'] = $data[0]['id'];
        // $row['pack_id'] = $data[0]['pack_id'];

        $id = $this->repository->insertPackLabelApi($row, $user_id);

        return $id;
    }
    public function insertPackLabel(array $data): int
    {
        $this->validator->validatePackLabelInsert($data);

        $row = $this->mapToRow($data);
        $row['label_id'] = $data['id'];
        $row['pack_id'] = $data['pack_id'];
        $user_id=(int)$this->session->get('user')["id"];

        $id = $this->repository->insertPackLabelApi($row, $user_id);

        return $id;
    }
    public function deletePackLabelApi(int $packId): void
    {
        $this->repository->deletePackLabel($packId);
    }
    public function deletePackLabelIDApi(int $labelId): void
    {
        $this->repository->deletePackLabelID($labelId);
    }
    public function deleteLabelInPackLabel(int $id): void
    {
        $this->repository->deleteLabelInPackLabel($id);
    }
    private function mapToRow(array $data): array
    {
        $result = [];
        if (isset($data['pack_id'])) {
            $result['pack_id'] = (string)$data['pack_id'];
        }
        if (isset($data['label_id'])) {
            $result['label_id'] = (string)$data['label_id'];
        }

        return $result;
    }
}
