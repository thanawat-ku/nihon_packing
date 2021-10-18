<?php

namespace App\Domain\TempQuery\Service;

use App\Domain\TempQuery\Repository\TempQueryRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class TempQueryValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(TempQueryRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('part_code', 'Input required')
            ->notEmptyString('part_name', 'Input required')
            ->notEmptyString('std_pack', 'Input required')
            ->notEmptyString('std_box', 'Input required');
    }
    public function validateTempQuery(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateTempQueryUpdate(string $part_code, array $data): void
    {
        $this->validateTempQuery($data);
    }
    public function validateTempQueryInsert( array $data): void
    {
        $this->validateTempQuery($data);
    }
}
