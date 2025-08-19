<?php

namespace App\Domain\TagSerial\Service;

use App\Domain\TagSerial\Repository\TagSerialRepository;
use App\Domain\TagSerial\Service\TagSerialValidator;
use App\Domain\TagSerial\Service\TagSerialFinder;
use App\Domain\Product\Service\ProductFinder;

/**
 * Service.
 */
final class TagSerialUpdater
{
    private $repository;
    private $finder;
    private $productFinder;
    private $validator;

    public function __construct(
        TagSerialRepository $repository,
        TagSerialValidator $validator,
        TagSerialFinder $finder,
        ProductFinder $productFinder,
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->finder = $finder;
        $this->productFinder = $productFinder;
    }

    public function insertTagSerial(array $data): int
    {
        $this->validator->validateTagSerialInsert($data);

        $row = $this->mapToTagSerialRow($data);

        $id = $this->repository->insertTagSerial($row);

        $data['serial_no'] = $this->finder->getTagSerialNo($data['customer_id']);
        $this->repository->updateTagSerial($id, $data);

        return $id;
    }

    public function updateTagSerial(int $scrapID, array $data): void
    {

        $this->validator->validateTagSerialUpdate($scrapID, $data);

        $row = $this->mapToTagSerialRow($data);

        $this->repository->updateTagSerial($scrapID, $row);
    }

    
    private function mapToTagSerialRow(array $data): array
    {
        $result = [];

        if (isset($data['tag_id'])) {
            $result['tag_id'] = (string)$data['tag_id'];
        }
        if (isset($data['customer_id'])) {
            $result['customer_id'] = (string)$data['customer_id'];
        }
        if (isset($data['serial_no'])) {
            $result['serial_no'] = (string)$data['serial_no'];
        }

        return $result;
    }
}
