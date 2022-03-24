<?php

namespace App\Domain\SplitLabelDetail\Service;

use App\Domain\SplitLabelDetail\Repository\SplitLabelDetailRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class SplitLabelDetailValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(SplitLabelDetailRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('split_label_id', 'Input required')
            ->notEmptyString('label_id', 'Input required');
    }
    public function validateSplitLabelDetail(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateSplitLabelDetailUpdate(string $splitLabelID ,array $data): void
    {
        /*
        if (!$this->repository->existsSplitLabelDetailNo($splitLabelNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stosplitLabelNoreId));
        }
        */
        $this->validateSplitLabelDetail($data);
    }
    public function validateSplitLabelDetailInsert( array $data): void
    {
        $this->validateSplitLabelDetail($data);
    }
}
