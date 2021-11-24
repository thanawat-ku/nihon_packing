<?php

namespace App\Domain\Scrap\Service;

use App\Domain\Scrap\Repository\ScrapRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class ScrapValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(ScrapRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('scrap_no', 'Input required')
            ->notEmptyString('scrap_date', 'Input required')
            ->notEmptyString('scrap_status', 'Input required');
    }
    public function validateScrap(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateScrapUpdate(string $lotNo, array $data): void
    {
        /*
        if (!$this->repository->existsLotNo($lotNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stolotNoreId));
        }
        */
        $this->validateScrap($data);
    }
    public function validateScrapInsert( array $data): void
    {
        $this->validateScrap($data);
    }
}
