<?php

namespace App\Domain\StockItem\Service;

use App\Domain\StockItem\Repository\StockItemRepository;
use App\Factory\ValidationFactory;
use Cake\Validation\Validator;
use Selective\Validation\Exception\ValidationException;

final class StockItemValidator
{
    private $repository;
    private $validationFactory;

    public function __construct(StockItemRepository $repository, ValidationFactory $validationFactory)
    {
        $this->repository = $repository;
        $this->validationFactory = $validationFactory;
    }

    private function createValidator(): Validator
    {
        $validator = $this->validationFactory->createValidator();

        return $validator
            ->notEmptyString('OutStockItemID', 'Input required');
    }
    public function validateStockItem(array $data): void
    {
        $validator = $this->createValidator();

        $validationResult = $this->validationFactory->createResultFromErrors(
            $validator->validate($data)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Please check your input', $validationResult);
        }
    }

    public function validateStockItemUpdate(string $part_code, array $data): void
    {
        /*
        if (!$this->repository->existsStockItemNo($stock_itemNo)) {
            throw new ValidationException(sprintf('Store not found: %s', $stostock_itemNoreId));
        }
        */
        $this->validateStockItem($data);
    }
    public function validateStockItemInsert( array $data): void
    {
        $this->validateStockItem($data);
    }
}
