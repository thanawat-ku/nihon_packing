<?php

namespace App\Domain\LotNonFullyPack\Service;

use App\Domain\LotNonFullyPack\Repository\LotNonFullyPackRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class LotNonFullyPackValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(LotNonFullyPackRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('lot_id', 'Input required')
            ->notEmptyString('label_id', 'Input required')
            ->notEmptyString('date', 'Input required')
            ->notEmptyString('is_register', 'Input required');
    }
    public function validateLotNonFullyPack(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateLotNonFullyPackUpdate(string $lotNo, array $data): void
    {
        /*
        if (!$this->repository->existsLotNo($lotNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stolotNoreId));
        }
        */
        $this->validateLotNonFullyPack($data);
    }
    public function validateLotNonFullyPackInsert(array $data): void
    {
        $this->validateLotNonFullyPack($data);
    }
}
