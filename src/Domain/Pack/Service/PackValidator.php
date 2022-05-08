<?php

namespace App\Domain\Pack\Service;

use App\Domain\Pack\Repository\PackRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class PackValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(PackRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('pack_no', 'Input required')
            ->notEmptyString('invoice_no', 'Input required')
            ->notEmptyString('pack_date', 'Input required')
            ->notEmptyString('product_id', 'Input required')
            ->notEmptyString('packing_id', 'Input required')
            ->notEmptyString('total_qty', 'Input required')
            ->notEmptyString('pack_status', 'Input required')
            ->notEmptyString('is_delete', 'Input required');
    }
    public function validatePack(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validatePackUpdate(string $part_code, array $data): void
    {
        /*
        if (!$this->repository->existsPackNo($productNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stoproductNoreId));
        }
        */
        $this->validatePack($data);
    }
    public function validatePackInsert(array $data): void
    {
        $this->validatePack($data);
    }
}
