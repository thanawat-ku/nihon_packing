<?php

namespace App\Domain\SellLabel\Service;

use App\Domain\SellLabel\Repository\SellLabelRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class SellLabelValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(SellLabelRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('sell_id', 'Input required')
            ->notEmptyString('label_id', 'Input required');
    }
    public function validateSellLabel(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateSellLabelUpdate(string $part_code, array $data): void
    {
        /*
        if (!$this->repository->existsSellLabelNo($productNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stoproductNoreId));
        }
        */
        $this->validateSellLabel($data);
    }
    public function validateSellLabelInsert( array $data): void
    {
        $this->validateSellLabel($data);
    }
}
