<?php

namespace App\Domain\SplitLabel\Service;

use App\Domain\SplitLabel\Repository\SplitLabelRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class SplitLabelValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(SplitLabelRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('split_label_no', 'Input required')
            ->notEmptyString('label_id', 'Input required')
            ->notEmptyString('status', 'Input required');
    }
    public function validateSplitLabel(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateSplitLabelUpdate(string $splitLabelID ,array $data): void
    {
        /*
        if (!$this->repository->existsSplitLabelNo($splitLabelNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stosplitLabelNoreId));
        }
        */
        $this->validateSplitLabel($data);
    }
    public function validateSplitLabelInsert( array $data): void
    {
        $this->validateSplitLabel($data);
    }
}
