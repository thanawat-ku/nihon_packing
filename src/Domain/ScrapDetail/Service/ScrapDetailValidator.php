<?php

namespace App\Domain\ScrapDetail\Service;

use App\Domain\ScrapDetail\Repository\ScrapDetailRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class ScrapDetailValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(ScrapDetailRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('scrap_id', 'Input required')
            ->notEmptyString('defect_id', 'Input required')
            ->notEmptyString('product_id', 'Input required')
            ->notEmptyString('section_id', 'Input required')
            ->notEmptyString('quantity', 'Input required');
    }
    public function validateScrapDetail(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateScrapDetailUpdate(string $lotNo, array $data): void
    {
        /*
        if (!$this->repository->existsLotNo($lotNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stolotNoreId));
        }
        */
        $this->validateScrapDetail($data);
    }
    public function validateScrapDetailInsert( array $data): void
    {
        $this->validateScrapDetail($data);
    }
}
