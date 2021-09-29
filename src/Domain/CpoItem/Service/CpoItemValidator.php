<?php

namespace App\Domain\CpoItem\Service;

use App\Domain\CpoItem\Repository\CpoItemRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class CpoItemValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(CpoItemRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('part_code', 'Input required')
            ->notEmptyString('part_name', 'Input required')
            ->notEmptyString('std_pack', 'Input required')
            ->notEmptyString('std_box', 'Input required');
    }
    public function validateCpoItem(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateCpoItemUpdate(string $part_code, array $data): void
    {
        /*
        if (!$this->repository->existsCpoItemNo($cpo_itemNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stocpo_itemNoreId));
        }
        */
        $this->validateCpoItem($data);
    }
    public function validateCpoItemInsert( array $data): void
    {
        $this->validateCpoItem($data);
    }
}
