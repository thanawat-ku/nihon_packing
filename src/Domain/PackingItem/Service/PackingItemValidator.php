<?php

namespace App\Domain\PackingItem\Service;

use App\Domain\PackingItem\Repository\PackingItemRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class PackingItemValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(PackingItemRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('PackingID', 'Input required')
            ->notEmptyString('LotID', 'Input required')
            ->notEmptyString('CpoItemID', 'Input required')
            ->notEmptyString('Quantity', 'Input required')
            ->notEmptyString('InvoiceItemID', 'Input required');
    }
    public function validatePackingItem(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validatePackingItemUpdate(int $packingID, $lotID, array $data): void
    {
        /*
        if (!$this->repository->existsPackingItemNo($packing_itemNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stopacking_itemNoreId));
        }
        */
        $this->validatePackingItem($data);
    }
    public function validatePackingItemInsert(array $data): void
    {
        $this->validatePackingItem($data);
    }
}
