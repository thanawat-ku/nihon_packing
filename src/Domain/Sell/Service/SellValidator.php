<?php

namespace App\Domain\Sell\Service;

use App\Domain\Sell\Repository\SellRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class SellValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(SellRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('sell_no', 'Input required')
            ->notEmptyString('sell_date', 'Input required')
            ->notEmptyString('product_id', 'Input required')
            ->notEmptyString('total_qty', 'Input required')
            ->notEmptyString('sell_status', 'Input required')
            ->notEmptyString('is_delete', 'Input required');
    }
    public function validateSell(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateSellUpdate(string $part_code, array $data): void
    {
        /*
        if (!$this->repository->existsSellNo($productNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stoproductNoreId));
        }
        */
        $this->validateSell($data);
    }
    public function validateSellInsert(array $data): void
    {
        $this->validateSell($data);
    }
}
