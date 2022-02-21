<?php

namespace App\Domain\Label\Service;

use App\Domain\Label\Repository\LabelRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class LabelValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(LabelRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('lot_id', 'Input required')
            ->notEmptyString('prefer_lot_id', 'Input required')
            ->notEmptyString('merge_pack_id', 'Input required')
            ->notEmptyString('split_label_id', 'Input required')
            ->notEmptyString('label_no', 'Input required')
            ->notEmptyString('label_type', 'Input required')
            ->notEmptyString('quantity', 'Input required')
            ->notEmptyString('status', 'Input required')
            ->notEmptyString('real_qty', 'Input required');
    }
    public function validateLabel(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateLabelUpdate(string $lotNo, array $data): void
    {
        /*
        if (!$this->repository->existsLotNo($lotNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stolotNoreId));
        }
        */
        $this->validateLabel($data);
    }
    public function validateLabelInsert( array $data): void
    {
        $this->validateLabel($data);
    }
}
