<?php

namespace App\Domain\MergePackDetail\Service;

use App\Domain\MergePackDetail\Repository\MergePackDetailRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class MergePackDetailValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(MergePackDetailRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('merge_pack_id', 'Input required')
            ->notEmptyString('label_id', 'Input required');
            
    }
    public function validateMergePackDetail(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateMergePackDetailUpdate(string $lotNo, array $data): void
    {
        /*
        if (!$this->repository->existsLotNo($lotNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stolotNoreId));
        }
        */
        $this->validateMergePackDetail($data);
    }
    public function validateMergePackDetailInsert( array $data): void
    {
        $this->validateMergePackDetail($data);
    }
}
