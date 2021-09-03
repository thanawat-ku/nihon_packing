<?php

namespace App\Domain\CreateMergeNoFromLabel\Service;

use App\Domain\CreateMergeNoFromLabel\Repository\CreateMergeNoFromLabelRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class CreateMergeNoFromLabelValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(CreateMergeNoFromLabelRepository $repository, ValidationFactory $validationFactory)
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
            ->notEmptyString('status', 'Input required')
            ->notEmptyString('product_id', 'Input required')
            ->notEmptyString('merge_no', 'Input required');
    }
    public function validateCreateMergeNoFromLabel(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateCreateMergeNoFromLabelUpdate(string $lotNo, array $data): void
    {
        /*
        if (!$this->repository->existsLotNo($lotNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stolotNoreId));
        }
        */
        $this->validateCreateMergeNoFromLabel($data);
    }
    public function validateCreateMergeNoFromLabelInsert( array $data): void
    {
        $this->validateCreateMergeNoFromLabel($data);
    }
}
