<?php

namespace App\Domain\ReportAll\Service;

use App\Domain\ReportAll\Repository\ReportAllRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class ReportAllValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(ReportAllRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('ReportAll_name', 'Input required');
    }
    public function validateReportAll(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateReportAllUpdate(string $lotNo, array $data): void
    {
        $this->validateReportAll($data);
    }
    public function validateReportAllInsert(array $data): void
    {
        $this->validateReportAll($data);
    }
}
