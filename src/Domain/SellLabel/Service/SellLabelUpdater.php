<?php

namespace App\Domain\SellLabel\Service;

use App\Domain\SellLabel\Repository\SellLabelRepository;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Service.
 */
final class SellLabelUpdater
{
    private $repository;
    private $validator;
    private $session;

    public function __construct(
        SellLabelRepository $repository,
        SellLabelValidator $validator,
        Session $session,
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->session = $session;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function insertSellLabelApi(array $data, $user_id): int
    {
        $this->validator->validateSellLabelInsert($data);

        $row = $this->mapToRow($data);
        $row['label_id'] = $data[0]['id'];
        $row['sell_id'] = $data[0]['sell_id'];

        $id = $this->repository->insertSellLabelApi($row, $user_id);

        return $id;
    }
    public function insertSellLabel(array $data): int
    {
        $this->validator->validateSellLabelInsert($data);

        $row = $this->mapToRow($data);
        $row['label_id'] = $data['id'];
        $row['sell_id'] = $data['sell_id'];
        $user_id=(int)$this->session->get('user')["id"];

        $id = $this->repository->insertSellLabelApi($row, $user_id);

        return $id;
    }
    public function deleteSellLabelApi(int $sellId): void
    {
        $this->repository->deleteSellLabel($sellId);
    }
    public function deleteSellLabelIDApi(int $labelId): void
    {
        $this->repository->deleteSellLabel($labelId);
    }
    public function deleteLabelInSellLabel(int $id): void
    {
        $this->repository->deleteLabelInSellLabel($id);
    }
    private function mapToRow(array $data): array
    {
        $result = [];
        if (isset($data['sell_id'])) {
            $result['sell_id'] = (string)$data['sell_id'];
        }
        if (isset($data['label_id'])) {
            $result['label_id'] = (string)$data['label_id'];
        }

        return $result;
    }
}
