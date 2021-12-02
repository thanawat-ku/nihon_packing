<?php

namespace App\Domain\Defect\Service;

use App\Domain\Defect\Repository\DefectRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class DefectValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(DefectRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('defect_name', 'Input required');
    }
    public function validateDefect(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateDefectUpdate(string $lotNo, array $data): void
    {
        $this->validateDefect($data);
    }
    public function validateDefectInsert(array $data): void
    {
        $this->validateDefect($data);
    }
}
