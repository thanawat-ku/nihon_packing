<?php

namespace App\Domain\Section\Service;

use App\Domain\Section\Repository\SectionRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class SectionValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(SectionRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('section_name', 'Input required');
    }
    public function validateSection(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateSectionUpdate(string $lotNo, array $data): void
    {
        $this->validateSection($data);
    }
    public function validateSectionInsert(array $data): void
    {
        $this->validateSection($data);
    }
}
