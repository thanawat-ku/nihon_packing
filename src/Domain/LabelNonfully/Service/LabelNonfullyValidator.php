<?php

namespace App\Domain\LabelNonfully\Service;

use App\Domain\LabelNonfully\Repository\LabelNonfullyRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class LabelNonfullyValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(LabelNonfullyRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('merge_pack_id', 'Input required')
            ->notEmptyString('label_type', 'Input required')
            ->notEmptyString('status', 'Input required');
            // ->notEmptyString('quantity', 'Input required');
    }
    public function validateLabelNonfully(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateLabelNonfullyUpdateApi(string $lotNo, array $data): void
    {
        /*
        if (!$this->repository->existsLotNo($lotNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stolotNoreId));
        }
        */
        $this->validateLabelNonfully($data);
    }
    public function validateLabelNonfullyInsert( array $data): void
    {
        $this->validateLabelNonfully($data);
    }
}
