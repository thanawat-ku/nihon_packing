<?php

namespace App\Domain\LabelVoidReason\Service;

use App\Domain\LabelVoidReason\Repository\LabelVoidReasonRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class LabelVoidReasonValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(LabelVoidReasonRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('reason_name', 'Input required')
            ->notEmptyString('description', 'Input required');
    }
    public function validateLabelVoidReason(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateLabelVoidReasonUpdate(string $lotNo, array $data): void
    {
        /*
        if (!$this->repository->existsPackLabelNo($productNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stoproductNoreId));
        }
        */
        $this->validateLabelVoidReason($data);
    }
    public function validateLabelVoidReasonInsert( array $data): void
    {
        $this->validateLabelVoidReason($data);
    }
}
