<?php

namespace App\Domain\PackLabel\Service;

use App\Domain\PackLabel\Repository\PackLabelRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class PackLabelValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(PackLabelRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('pack_id', 'Input required')
            ->notEmptyString('label_id', 'Input required');
    }
    public function validatePackLabel(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validatePackLabelUpdate(string $part_code, array $data): void
    {
        /*
        if (!$this->repository->existsPackLabelNo($productNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stoproductNoreId));
        }
        */
        $this->validatePackLabel($data);
    }
    public function validatePackLabelInsert( array $data): void
    {
        $this->validatePackLabel($data);
    }
}
