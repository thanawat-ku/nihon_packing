<?php

namespace App\Domain\LabelPackMerge\Service;

use App\Domain\LabelPackMerge\Repository\LabelPackMergeRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class LabelPackMergeValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(LabelPackMergeRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('lot_id', 'Input required')
            ->notEmptyString('merge_pack_id', 'Input required')
            ->notEmptyString('label_no', 'Input required')
            ->notEmptyString('label_type', 'Input required')
            ->notEmptyString('quantity', 'Input required')
            ->notEmptyString('status', 'Input required');
    }
    public function validateLabelPackMerge(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateLabelPackMergeUpdate(string $lotNo, array $data): void
    {
        /*
        if (!$this->repository->existsLotNo($lotNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stolotNoreId));
        }
        */
        $this->validateLabelPackMerge($data);
    }
    public function validateLabelPackMergeInsert( array $data): void
    {
        $this->validateLabelPackMerge($data);
    }
}
