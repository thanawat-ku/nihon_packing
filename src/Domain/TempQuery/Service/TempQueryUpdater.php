<?php

namespace App\Domain\TempQuery\Service;

use App\Domain\TempQuery\Repository\TempQueryRepository;

/**
 * Service.
 */
final class TempQueryUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        TempQueryRepository $repository,
        TempQueryValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
            //->addFileHandler('store_updater.log')
            //->createInstance();
    }

    public function insertTempQuery( array $data): int
    {
        $this->validator->validateTempQueryInsert($data);

        $row = $this->mapToTempQueryRow($data);

        $id=$this->repository->insertTempQuery($row);

        return $id;
    }
    private function mapToTempQueryRow(array $data): array
    {
        $result = [];

        if (isset($data['uuid'])) {
            $result['uuid'] = (string)$data['uuid'];
        }
        if (isset($data['cpo_no'])) {
            $result['cpo_no'] = (string)$data['cpo_no'];
        }
        if (isset($data['po_no'])) {
            $result['po_no'] = (string)$data['po_no'];
        }
        if (isset($data['cpo_id'])) {
            $result['cpo_id'] = (string)$data['cpo_id'];
        }
        if (isset($data['cpo_item_id'])) {
            $result['cpo_item_id'] = (string)$data['cpo_item_id'];
        }
        if (isset($data['product_id'])) {
            $result['product_id'] = (string)$data['product_id'];
        }
        if (isset($data['quantity'])) {
            $result['quantity'] = (string)$data['quantity'];
        }
        if (isset($data['packing_qty'])) {
            $result['packing_qty'] = (string)$data['packing_qty'];
        }
        if (isset($data['due_date'])) {
            $result['due_date'] = (string)$data['due_date'];
        }

        return $result;
    }
}