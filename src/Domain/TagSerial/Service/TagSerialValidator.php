<?php

namespace App\Domain\TagSerial\Service;

use App\Domain\TagSerial\Repository\TagSerialRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class TagSerialValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(TagSerialRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('tag_id', 'Input required')
            ->notEmptyString('customer_id', 'Input required');
    }
    public function validateTagSerial(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateTagSerialUpdate(string $lotNo, array $data): void
    {
        $this->validateTagSerial($data);
    }
    public function validateTagSerialInsert( array $data): void
    {
        $this->validateTagSerial($data);
    }
}
