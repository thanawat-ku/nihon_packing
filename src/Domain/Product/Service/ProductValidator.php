<?php

namespace App\Domain\Product\Service;

use App\Domain\Product\Repository\ProductRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class ProductValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(ProductRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('product_no', 'Input required')
            ->notEmptyString('product_id', 'Input required')
            ->notEmptyString('quantity', 'Input required');
    }
    public function validateProduct(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateProductUpdate(string $productNo, array $data): void
    {
        /*
        if (!$this->repository->existsProductNo($productNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stoproductNoreId));
        }
        */
        $this->validateProduct($data);
    }
    public function validateProductInsert( array $data): void
    {
        $this->validateProduct($data);
    }
}
