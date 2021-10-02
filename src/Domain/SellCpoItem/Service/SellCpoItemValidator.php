<?php

namespace App\Domain\SellCpoItem\Service;

use App\Domain\SellCpoItem\Repository\SellCpoItemRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class SellCpoItemValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(SellCpoItemRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('sell_id', 'Input required')
            ->notEmptyString('cpo_item_id', 'Input required')
            ->notEmptyString('remain_qty', 'Input required')
            ->notEmptyString('sell_qty', 'Input required');
           
    }
    public function validateSellCpoItem(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateSellCpoItemUpdate(string $part_code, array $data): void
    {
        /*
        if (!$this->repository->existsSellCpoItemNo($productNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stoproductNoreId));
        }
        */
        $this->validateSellCpoItem($data);
    }
    public function validateSellCpoItemInsert( array $data): void
    {
        $this->validateSellCpoItem($data);
    }
}
