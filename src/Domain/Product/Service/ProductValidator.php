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
            ->notEmptyString('product_code', 'Input required')
            ->notEmptyString('product_name', 'Input required')
            ->notEmptyString('price', 'Input required')
            ->notEmptyString('std_pack', 'Input required')
            ->notEmptyString('std_box', 'Input required');
    }
<<<<<<< HEAD
=======
    
>>>>>>> tae
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

<<<<<<< HEAD
    public function validateProductUpdate(string $product_code, array $data): void
    {
        /*
        if (!$this->repository->existsProductNo($productNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stoproductNoreId));
=======
    public function validateProductUpdate(string $lotNo, array $data): void
    {
        /*
        if (!$this->repository->existsLotNo($lotNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stolotNoreId));
>>>>>>> tae
        }
        */
        $this->validateProduct($data);
    }
    public function validateProductInsert( array $data): void
    {
        $this->validateProduct($data);
    }
}
