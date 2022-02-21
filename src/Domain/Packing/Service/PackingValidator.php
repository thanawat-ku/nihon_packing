<?php

namespace App\Domain\Packing\Service;

use App\Domain\Packing\Repository\PackingRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class PackingValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(PackingRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('PackingID', 'Input required')
            ->notEmptyString('PackingNo', 'Input required')
            ->notEmptyString('PackingNum', 'Input required')
            ->notEmptyString('IssueDate', 'Input required')
            ->notEmptyString('DateTime', 'Input required')
            ->notEmptyString('UserID', 'Input required');
    }
    public function validatePacking(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validatePackingUpdate(string $part_code, array $data): void
    {
        /*
        if (!$this->repository->existsPackingNo($packingNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stopackingNoreId));
        }
        */
        $this->validatePacking($data);
    }
    public function validatePackingInsert(array $data): void
    {
        $this->validatePacking($data);
    }
}
