<?php

namespace App\Domain\Lot\Service;

use App\Domain\Lot\Repository\LotRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class LotValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(LotRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('lot_no', 'Input required')
            ->notEmptyString('product_id', 'Input required')
            ->notEmptyString('quantity', 'Input required');
    }
    
    public function validateLot(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateLotUpdate(string $lotNo, array $data): void
    {
        /*
        if (!$this->repository->existsLotNo($lotNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stolotNoreId));
        }
        */
        $this->validateLot($data);
    }
    public function validateLotInsert( array $data): void
    {
        $this->validateLot($data);
    }
}
