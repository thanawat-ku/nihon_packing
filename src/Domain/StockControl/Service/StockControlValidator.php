<?php

namespace App\Domain\StockControl\Service;

use App\Domain\StockControl\Repository\StockControlRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class StockControlValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(StockControlRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('SectionID', 'Input required')
            ->notEmptyString('VendorID', 'Input required')
            ->notEmptyString('DocNo', 'Input required')
            ->notEmptyString('DocNum', 'Input required')
            ->notEmptyString('issueDate', 'Input required')
            ->notEmptyString('UpdateTime', 'Input required')
            ->notEmptyString('UserID', 'Input required')
            ->notEmptyString('IsPrint', 'Input required');
    }
    public function validateStockControl(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateStockControlUpdate(string $part_code, array $data): void
    {
        /*
        if (!$this->repository->existsStockControlNo($stock_controlNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stostock_controlNoreId));
        }
        */
        $this->validateStockControl($data);
    }
    public function validateStockControlInsert(array $data): void
    {
        $this->validateStockControl($data);
    }
}
