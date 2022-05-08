<?php

namespace App\Domain\PackCpoItem\Service;

use App\Domain\PackCpoItem\Repository\PackCpoItemRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class PackCpoItemValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(PackCpoItemRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('pack_id', 'Input required')
            ->notEmptyString('cpo_item_id', 'Input required')
            ->notEmptyString('remain_qty', 'Input required')
            ->notEmptyString('pack_qty', 'Input required');
           
    }
    public function validatePackCpoItem(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validatePackCpoItemUpdate(string $part_code, array $data): void
    {
        $this->validatePackCpoItem($data);
    }
    public function validatePackCpoItemInsert( array $data): void
    {
        $this->validatePackCpoItem($data);
    }
}
