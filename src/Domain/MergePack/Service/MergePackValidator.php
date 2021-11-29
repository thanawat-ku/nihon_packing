<?php

namespace App\Domain\MergePack\Service;

use App\Domain\MergePack\Repository\MergePackRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class MergePackValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(MergePackRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('merge_no', 'Input required')
            ->notEmptyString('product_id', 'Input required')
            ->notEmptyString('merge_status', 'Input required')
            ->notEmptyString('part_code', 'Input required')
            ->notEmptyString('is_delete', 'Input required');
    }
    public function validateMergePack(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateMergePackUpdate(string $lotNo, array $data): void
    {
        /*
        if (!$this->repository->existsLotNo($lotNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stolotNoreId));
        }
        */
        $this->validateMergePack($data);
    }
    public function validateMergePackInsert(array $data): void
    {
        $this->validateMergePack($data);
    }
}
