<?php

namespace App\Domain\CheckLabel\Service;

use App\Domain\CheckLabel\Repository\CheckLabelRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class CheckLabelValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(CheckLabelRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('lot_id', 'Input required')
            ->notEmptyString('label_no', 'Input required')
            ->notEmptyString('label_type', 'Input required')
            ->notEmptyString('quantity', 'Input required');
    }
    public function validateCheckLabel(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateCheckLabelUpdate(string $lotNo, array $data): void
    {
        /*
        if (!$this->repository->existsLotNo($lotNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stolotNoreId));
        }
        */
        $this->validateCheckLabel($data);
    }
    public function validateCheckLabelInsert( array $data): void
    {
        $this->validateCheckLabel($data);
    }
}
